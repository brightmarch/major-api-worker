<?php

namespace MajorApi\Tests\Functional\Quickbooks;

use MajorApi\Quickbooks\IppClient;
use MajorApi\Library\Registry;
use MajorApi\Tests\Functional\TestCase;

/**
 * @group FunctionalTests
 * @group SlowTests
 */
class IppDesktopClientTest extends TestCase
{

    public function testAccessRequiresAuthorization()
    {
        $majorApiConfig = Registry::getMajorApiConfig();
        $ippClient = IppClient::getDesktopClient(
            $majorApiConfig['test_ipp_oauth_consumer_key'],
            $majorApiConfig['test_ipp_oauth_consumer_secret']
        );

        $ippClient->connect(
            $majorApiConfig['test_ipp_oauth_token'],
            $majorApiConfig['test_ipp_oauth_token_secret'],
            'invalid'
        );

        $response = $ippClient->read('company');

        $this->assertFalse($response);
        $this->assertFalse($ippClient->isSuccessfulRequest());
        $this->assertNotEmpty($ippClient->getLastResponseErrorMessage());
    }

    public function testReadingCompanyFile()
    {
        $ippClient = $this->getClient();
        $response = $ippClient->read('company');

        $this->assertTrue($response);
        $this->assertTrue($ippClient->isSuccessfulRequest());
        $this->assertEmpty($ippClient->getLastResponseErrorMessage());
        $this->assertNotEmpty($ippClient->getLastResponse());
    }

    public function testReadingAllItems()
    {
        $ippClient = $this->getClient();
        $response = $ippClient->read('item');

        $this->assertTrue($response);
        $this->assertTrue($ippClient->isSuccessfulRequest());
        $this->assertEmpty($ippClient->getLastResponseErrorMessage());
        $this->assertNotEmpty($ippClient->getLastResponse());
    }

    public function _testCreatingCustomer()
    {
        $xml = '<?xml version="1.0" encoding="UTF-16"?>
<Add xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" RequestId="%s" xmlns="http://www.intuit.com/sb/cdm/v2" FullResponse="true">
   <ExternalRealmId>156234822</ExternalRealmId>
   <Object xsi:type="Customer">
   <TypeOf>Person</TypeOf>
   <Name>Billy James</Name>
   <Address>
      <Line1>1000 James Way</Line1>
      <City>Dallas</City>
      <Country>USA</Country>
      <CountrySubDivisionCode>TX</CountrySubDivisionCode>
      <PostalCode>75228</PostalCode>
      <Tag>Billing</Tag>
   </Address>
   <DBAName>James, Inc.</DBAName>
   <AcctNum>4111555</AcctNum>
   </Object>
</Add>';

        $xml = sprintf($xml, md5(uniqid() . (string)microtime(true)));

        $ippClient = $this->getClient();
        $ippClient->create('customer', $xml);

        var_dump($ippClient->getLastResponse());
    }

    private function getClient()
    {
        $majorApiConfig = Registry::getMajorApiConfig();
        $ippClient = IppClient::getDesktopClient(
            $majorApiConfig['test_ipp_oauth_consumer_key'],
            $majorApiConfig['test_ipp_oauth_consumer_secret']
        );

        $ippClient->connect(
            $majorApiConfig['test_ipp_oauth_token'],
            $majorApiConfig['test_ipp_oauth_token_secret'],
            $majorApiConfig['test_ipp_realm_id']
        );

        return $ippClient;
    }

}
