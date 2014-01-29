<?php

namespace MajorApi\Tests\Unit\Quickbooks;

use MajorApi\Quickbooks\IppClient;
use MajorApi\Tests\Unit\TestCase;

/**
 * @group UnitTests
 */
class IppClientTest extends TestCase
{

    public function testGettingDesktopClient()
    {
        $ippClient = IppClient::getDesktopClient(uniqid(), uniqid());

        $this->assertInstanceOf('MajorApi\Quickbooks\IppDesktopClient', $ippClient);
    }

    public function testGettingOnlineClient()
    {
        $ippClient = IppClient::getOnlineClient(uniqid(), uniqid());

        $this->assertInstanceOf('MajorApi\Quickbooks\IppOnlineClient', $ippClient);
    }

    public function testConnectedClientRequiresTokenAndTokenSecret()
    {
        $ippClient = IppClient::getDesktopClient(uniqid(), uniqid());
        $this->assertFalse($ippClient->isConnected());

        $ippClient->connect(uniqid(), uniqid(), mt_rand(1, 100));
        $this->assertTrue($ippClient->isConnected());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testReadingResourceRequiresConnection()
    {
        $ippClient = IppClient::getDesktopClient(uniqid(), uniqid());
        $ippClient->read('item');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testCreatingResourceRequiresConnection()
    {
        $ippClient = IppClient::getDesktopClient(uniqid(), uniqid());
        $ippClient->create('item', '<xml><item></item></xml>');
    }

    public function testReadingResourceRequiresAuthorization()
    {
        $responseXml = file_get_contents($this->fixtureDir . '/ipp-unauthorized-response.xml');
        $mockOAuth = $this->getOAuthMock($responseXml);

        $ippClient = IppClient::getDesktopClient(uniqid(), uniqid());
        $ippClient->setOAuth($mockOAuth);
        $ippClient->connect(uniqid(), uniqid(), mt_rand(1, 100));

        $read = $ippClient->read('item');

        $this->assertFalse($read);
        $this->assertFalse($ippClient->isSuccessfulRequest());
        $this->assertNotEmpty($ippClient->getLastResponseErrorMessage());
    }

    public function testReadingResource()
    {
        $responseXml = file_get_contents($this->fixtureDir . '/ipp-company-query-valid.xml');
        $mockOAuth = $this->getOAuthMock($responseXml);

        $ippClient = IppClient::getDesktopClient(uniqid(), uniqid());
        $ippClient->setOAuth($mockOAuth);
        $ippClient->connect(uniqid(), uniqid(), mt_rand(1, 100));

        $read = $ippClient->read('company');

        $this->assertTrue($read);
        $this->assertTrue($ippClient->isSuccessfulRequest());
        $this->assertEmpty($ippClient->getLastResponseErrorMessage());
        $this->assertNotEmpty($ippClient->getLastResponse());
    }

    public function testCreatingResourceRequiresValidRequest()
    {
        $requestXml = '';
        $responseXml = file_get_contents($this->fixtureDir . '/ipp-invalid-creation-response.xml');
        $mockOAuth = $this->getOAuthMock($responseXml);

        $ippClient = IppClient::getDesktopClient(uniqid(), uniqid());
        $ippClient->setOAuth($mockOAuth);
        $ippClient->connect(uniqid(), uniqid(), mt_rand(1, 100));

        $created = $ippClient->create('account', $requestXml);

        $this->assertFalse($created);
        $this->assertFalse($ippClient->isSuccessfulRequest());
        $this->assertNotEmpty($ippClient->getLastResponseErrorMessage());
    }

    public function testCreatingResource()
    {
        $requestXml = '';
        $responseXml = file_get_contents($this->fixtureDir . '/ipp-creation-response.xml');
        $mockOAuth = $this->getOAuthMock($responseXml);

        $ippClient = IppClient::getDesktopClient(uniqid(), uniqid());
        $ippClient->setOAuth($mockOAuth);
        $ippClient->connect(uniqid(), uniqid(), mt_rand(1, 100));

        $created = $ippClient->create('account', $requestXml);

        $this->assertTrue($created);
        $this->assertTrue($ippClient->isSuccessfulRequest());
        $this->assertEmpty($ippClient->getLastResponseErrorMessage());
        $this->assertNotEmpty($ippClient->getLastResponse());
    }

    private function getOAuthMock($responseXml)
    {
        $mockOAuth = $this->getMockBuilder('OAuth')
            ->disableOriginalConstructor()
            ->setMethods(['setToken', 'fetch', 'getLastResponseInfo', 'getLastResponse'])
            ->getMock();

        $mockOAuth->expects($this->once())
            ->method('setToken')
            ->will($this->returnValue(true));
        $mockOAuth->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue(true));
        $mockOAuth->expects($this->once())
            ->method('getLastResponseInfo')
            ->will($this->returnValue(['http_code' => 200]));
        $mockOAuth->expects($this->once())
            ->method('getLastResponse')
            ->will($this->returnValue($responseXml));

        return $mockOAuth;
    }
}
