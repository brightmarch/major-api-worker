<?php

namespace MajorApi\Tests\Functional\Quickbooks;

use MajorApi\Quickbooks\QbxmlResponseProcessor;
use MajorApi\Library\Registry;
use MajorApi\Tests\Functional\TestCase;

/**
 * @group FunctionalTests
 */
class QbxmlResponseProcessorTest extends TestCase
{

    /**
     * @dataProvider providerQuickbooksResponse
     */
    public function testHandlingResponse($xmlFile, $persister)
    {
        $postgres = Registry::getPostgres();
        $application = $this->getApplicationFixture();

        $xmlPath = $this->fixtureDir . '/' . $xmlFile;
        $xml = file_get_contents($xmlPath);
        $xmlHash = md5($xml);

        $parameters = [
            'created' => Registry::getTimeString(),
            'updated' => Registry::getTimeString(),
            'status' => Registry::STATUS_ENABLED,
            'application_id' => $application['id'],
            'command' => 'MockCommand',
            'persister' => $persister
        ];

        $postgres->insert('api_quickbooks_queue', $parameters);
        $quickbooksQueueId = (int)$postgres->lastInsertId('api_quickbooks_queue_id_seq');

        $qbxmlResponseProcessor = new QbxmlResponseProcessor($postgres, $application['id']);

        $this->assertTrue($qbxmlResponseProcessor->handle($xml, $xmlHash));
    }

    public function testHandlingMultipleResponses()
    {
        $postgres = Registry::getPostgres();
        $application = $this->getApplicationFixture();

        $xmlPath = $this->fixtureDir . '/quickbooks-host-query-item-query-valid.xml';
        $xml = file_get_contents($xmlPath);
        $xmlHash = md5($xml);

        $parameters = [
            'created' => Registry::getTimeString(),
            'updated' => Registry::getTimeString(),
            'status' => Registry::STATUS_ENABLED,
            'application_id' => $application['id'],
            'command' => 'MockCommand',
            'persister' => 'ItemQueryPersister'
        ];

        // Create the first QuickBooks Queue record.
        $postgres->insert('api_quickbooks_queue', $parameters);

        // Create a second QuickBooks Queue record with a different persister.
        $parameters['persister'] = 'HostQueryPersister';
        $postgres->insert('api_quickbooks_queue', $parameters);
        
        $qbxmlResponseProcessor = new QbxmlResponseProcessor($postgres, $application['id']);

        $this->assertTrue($qbxmlResponseProcessor->handle($xml, $xmlHash));
    }

    public function providerQuickbooksResponse()
    {
        // Uncomment the quickbooks-item-query-valid-large.xml element
        // below to run thousands of QuickBooks Items through the QbxmlResponseProcessor.
        $provider = [
            ['quickbooks-customer-add-valid.xml', 'CustomerAddPersister'],
            ['quickbooks-customer-query-valid.xml', 'CustomerQueryPersister'],
            ['quickbooks-host-query-valid.xml', 'HostQueryPersister'],
            ['quickbooks-host-query-valid.xml', 'HostQueryPersister'],
            ['quickbooks-item-query-valid-discount.xml', 'ItemQueryPersister'],
            ['quickbooks-item-query-valid-fixed-asset.xml', 'ItemQueryPersister'],
            ['quickbooks-item-query-valid-group.xml', 'ItemQueryPersister'],
            ['quickbooks-item-query-valid-inventory.xml', 'ItemQueryPersister'],
            ['quickbooks-item-query-valid-non-inventory.xml', 'ItemQueryPersister'],
            ['quickbooks-item-query-valid-service.xml', 'ItemQueryPersister'],
            ['quickbooks-item-query-valid.xml', 'ItemQueryPersister'],
            ['quickbooks-sales-rep-query-valid.xml', 'SalesRepQueryPersister'],
            //['quickbooks-item-query-valid-large.xml', 'ItemQueryPersister']
        ];

        return $provider;
    }

}
