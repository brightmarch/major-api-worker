<?php

namespace MajorApi\Tests\Functional\Quickbooks\Command;

use MajorApi\Quickbooks\Command\CustomerAddCommand;
use MajorApi\Quickbooks\IppClient;
use MajorApi\Library\Registry;
use MajorApi\Tests\Functional\TestCase;

/**
 * @group FunctionalTests
 */
class CustomerAddCommandTest extends TestCase
{

    public function testAddingCustomer()
    {
        $majorApiConfig = Registry::getMajorApiConfig();
        $postgres = Registry::getPostgres();
        $application = $this->getIppApplicationFixture();

        $responseXmlFilePath = sprintf('%s/%s', $this->fixtureDir, 'ipp-customer-add-response.xml');
        $responseXml = file_get_contents($responseXmlFilePath);

        // The IppClient object is mocked so we do not have to actually communicate
        // with IPP so the tests run fast and error free.
        $mockIppClient = $this->getMockBuilder('MajorApi\Quickbooks\IppDesktopClient')
            ->disableOriginalConstructor()
            ->setMethods(['getRequestId', 'create', 'getLastResponse'])
            ->getMock();
        $mockIppClient->expects($this->once())
            ->method('getRequestId')
            ->will($this->returnValue(uniqid()));
        $mockIppClient->expects($this->once())
            ->method('create')
            ->will($this->returnValue('true'));
        $mockIppClient->expects($this->once())
            ->method('getLastResponse')
            ->will($this->returnValue($responseXml));

        // Create a valid api_quickbooks_queue record that will be used
        // to reference when the request is finished.
        $quickbooksQueue = [
            'created' => Registry::getTimeString(),
            'updated' => Registry::getTimeString(),
            'status' => Registry::STATUS_ENABLED,
            'application_id' => $application['id'],
            'command' => 'CustomerAddCommand',
            'persister' => 'CustomerAddPersister',
            'token' => $application['quickbooks_token'],
            'request_xml' => null,
            'is_ipp' => 't',
            'queue_token' => md5(time())
        ];

        $postgres->insert('api_quickbooks_queue', $quickbooksQueue);
        $quickbooksQueueId = (int)$postgres->lastInsertId('api_quickbooks_queue_id_seq');

        // Create a valid customer to send to the mock IPP resource.
        $name = 'WILLY LOMAN';
        $quickbooksNameToken = md5($name);

        $quickbooksCustomer = [
            'created' => Registry::getTimeString(),
            'updated' => Registry::getTimeString(),
            'status' => Registry::STATUS_ENABLED,
            'application_id' => $application['id'],
            'name' => $name,
            'quickbooks_name_token' => $quickbooksNameToken,
            'bill_address1' => '3623 Prader Ct',
            'bill_city' => 'Dallas',
            'bill_state' => 'TX',
            'bill_country' => 'USA',
            'bill_postal_code' => '75228',
            'ship_address1' => '3623 Prader Ct',
            'ship_city' => 'Dallas',
            'ship_state' => 'TX',
            'ship_country' => 'USA',
            'ship_postal_code' => '75228',
            'phone' => '214-641-6201',
            'email' => 'vic@brightmarch.com',
            'company_name' => 'Bright March'
        ];

        $postgres->insert('api_quickbooks_customer', $quickbooksCustomer);
        $objectId = $postgres->lastInsertId('api_quickbooks_customer_id_seq');

        $command = new CustomerAddCommand(
            $postgres,
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

        $quickbooksCustomers = $parser->parse();

        $this->assertGreaterThan(0, $quickbooksCustomers->count());
        $this->assertInstanceOf('MajorApi\Quickbooks\Parser\Ipp\CustomerAddParser', $parser);
    }

}
