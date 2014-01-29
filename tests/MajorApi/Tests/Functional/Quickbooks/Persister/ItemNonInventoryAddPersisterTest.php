<?php

namespace MajorApi\Tests\Functional\Quickbooks\Persister;

use MajorApi\Quickbooks\Persister\ItemNonInventoryAddPersister;
use MajorApi\Library\Registry;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath,
    \ReflectionClass;

/**
 * @group FunctionalTests
 */
class ItemNonInventoryAddPersisterTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testPersistingUpdatedItem($parserClass, $validXml)
    {
        $postgres = Registry::getPostgres();
        $application = $this->getApplicationFixture();

        $xmlPath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlPath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $class = new ReflectionClass($parserClass);
        $parser = $class->newInstance($xml, $dom, $xpath);

        $container = $parser->load()
            ->initialize()
            ->parse();

        // Create a customer that will be modified by the ItemNonInventoryAddPersister.
        $quickbooksItem = [
            'created' => Registry::getTimeString(),
            'updated' => Registry::getTimeString(),
            'status' => Registry::STATUS_ENABLED,
            'application_id' => $application['id'],
            'type' => 'non-inventory',
            'name' => $container[0]['name']
        ];

        $postgres->insert('api_quickbooks_item', $quickbooksItem);

        // Persist the QuickBooks CustomerAdd results
        $persister = new ItemNonInventoryAddPersister($postgres, $parser, $application['id']);
        $this->assertTrue($persister->persist());

        $quickbooksCustomer = $postgres->fetchAssoc(
            $persister->getSelectQuery(),
            $persister->getSelectParameters($container[0])
        );

        $this->assertEquals($container[0]['quickbooks_list_id'], $quickbooksCustomer['quickbooks_list_id']);
        $this->assertEquals($container[0]['quickbooks_edit_sequence'], $quickbooksCustomer['quickbooks_edit_sequence']);
    }

    /**
     * @dataProvider providerValidXml
     */
    public function testPersistingInvalidItem($parserClass, $validXml)
    {
        $postgres = Registry::getPostgres();
        $application = $this->getApplicationFixture();

        $xmlPath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlPath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $class = new ReflectionClass($parserClass);
        $parser = $class->newInstance($xml, $dom, $xpath);

        $persister = new ItemNonInventoryAddPersister($postgres, $parser, $application['id']);

        $this->assertTrue($persister->persist());
    }

    public function providerValidXml()
    {
        $provider = [
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemNonInventoryAddParser', 'quickbooks-item-non-inventory-add-valid.xml']
        ];

        return $provider;
    }

}
