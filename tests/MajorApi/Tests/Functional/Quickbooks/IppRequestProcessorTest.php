<?php

namespace MajorApi\Tests\Functional\Quickbooks;

use MajorApi\Quickbooks\IppRequestProcessor;
use MajorApi\Library\Registry;
use MajorApi\Tests\Functional\TestCase;

/**
 * @group FunctionalTests
 */
class IppRequestProcessorTest extends TestCase
{

    /**
     * @group SlowTests
     */
    public function testHandlingCommand()
    {
        // This is a really shitty test because it has to actually
        // connect with IPP and send data. It does a simple ItemQueryCommand
        // which will just send data back about the items, but it still takes
        // a long time to run.
        $objectId = 0;
        $quickbooksQueueId = 0;

        $postgres = Registry::getPostgres();
        $twig = Registry::getTwig();
        $majorApiConfig = Registry::getMajorApiConfig();

        $application = $this->getIppApplicationFixture();

        // Create a quickbooks_queue record.
        $quickbooksQueue = [
            'created' => Registry::getTimeString(),
            'updated' => Registry::getTimeString(),
            'status' => Registry::STATUS_ENABLED,
            'application_id' => $application['id'],
            'command' => 'ItemQueryCommand',
            'persister' => 'ItemQueryPersister',
            'token' => $application['quickbooks_token'],
            'request_xml' => null,
            'is_ipp' => 't',
            'queue_token' => md5(time())
        ];

        $postgres->insert('api_quickbooks_queue', $quickbooksQueue);
        $quickbooksQueueId = $postgres->lastInsertId('api_quickbooks_queue_id_seq');

        $ippRequestProcessor = new IppRequestProcessor(
            $postgres,
            $twig,
            $application['id'],
            $quickbooksQueueId,
            $objectId,
            $majorApiConfig['test_ipp_oauth_consumer_key'],
            $majorApiConfig['test_ipp_oauth_consumer_secret']
        );

        // Run the IppRequestProcessor handling function.
        $handled = $ippRequestProcessor->handle();

        $this->assertTrue($handled);

        // Get the api_quickbooks_queue record we just created and make sure it has
        // some essential values proving it was handled properly.
        $query = "SELECT qq.* FROM api_quickbooks_queue qq WHERE qq.id = ?";
        $quickbooksQueue = $postgres->fetchAssoc($query, [$quickbooksQueueId]);

        $this->assertNotEmpty($quickbooksQueue['processed']);
        $this->assertNotEmpty($quickbooksQueue['request_xml']);
    }

}
