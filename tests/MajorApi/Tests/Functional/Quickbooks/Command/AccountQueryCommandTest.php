<?php

namespace MajorApi\Tests\Functional\Quickbooks\Command;

use MajorApi\Quickbooks\Command\AccountQueryCommand;
use MajorApi\Quickbooks\IppClient;
use MajorApi\Library\Registry;
use MajorApi\Tests\Functional\TestCase;

/**
 * @group FunctionalTests
 */
class AccountQueryCommandTest extends TestCase
{

    /**
     * @expectedException MajorApi\Quickbooks\Parser\Exception\Exception
     */
    public function testQueryingAccountsRequiresIppApplication()
    {
        $quickbooksQueueId = 0;
        $objectId = 0;

        $application = $this->getApplicationFixture();

        $command = new AccountQueryCommand(
            Registry::getPostgres(),
            Registry::getTwig(),
            $application['id'],
            $quickbooksQueueId,
            $objectId,
            uniqid(),
            uniqid()
        );

        $parser = $command->execute();
        $parser->load();
    }

    public function testQueryingAccounts()
    {
        $quickbooksQueueId = 0;
        $objectId = 0;

        $responseXmlFilePath = sprintf('%s/%s', $this->fixtureDir, 'ipp-account-query-response.xml');
        $responseXml = file_get_contents($responseXmlFilePath);

        $majorApiConfig = Registry::getMajorApiConfig();
        $application = $this->getIppApplicationFixture();

        $mockIppClient = $this->getMockBuilder('MajorApi\Quickbooks\IppDesktopClient')
            ->disableOriginalConstructor()
            ->setMethods(['read', 'getLastResponse'])
            ->getMock();
        $mockIppClient->expects($this->once())
            ->method('read')
            ->will($this->returnValue('true'));
        $mockIppClient->expects($this->once())
            ->method('getLastResponse')
            ->will($this->returnValue($responseXml));

        $command = new AccountQueryCommand(
            Registry::getPostgres(),
            Registry::getTwig(),
            $application['id'],
            $quickbooksQueueId,
            $objectId,
            $majorApiConfig['test_ipp_oauth_consumer_key'],
            $majorApiConfig['test_ipp_oauth_consumer_secret']
        );

        $command->setIppClient($mockIppClient);
        $parser = $command->execute();

        $parser->load();
        $parser->initialize();

        $quickbooksAccounts = $parser->parse();

        $this->assertGreaterThan(0, $quickbooksAccounts->count());
        $this->assertInstanceOf('MajorApi\Quickbooks\Parser\Ipp\AccountQueryParser', $parser);
    }

}
