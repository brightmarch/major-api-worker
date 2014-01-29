<?php

namespace MajorApi\Tests\Functional\Quickbooks\Persister;

use MajorApi\Quickbooks\Persister\ItemQueryPersister;
use MajorApi\Library\Registry;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath,
    \ReflectionClass;

/**
 * @group FunctionalTests
 */
class ItemQueryPersisterTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testPersistingItem($parserClass, $validXml)
    {
        $postgres = Registry::getPostgres();
        $application = $this->getApplicationFixture();

        $xmlPath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlPath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $class = new ReflectionClass($parserClass);
        $parser = $class->newInstance($xml, $dom, $xpath);

        $persister = new ItemQueryPersister($postgres, $parser, $application['id']);
        $this->assertTrue($persister->persist());

        $container = $persister->getParser()
            ->getContainer();

        $quickbooksItem = $postgres->fetchAssoc(
            $persister->getSelectQuery(),
            $persister->getSelectParameters($container[0])
        );

        $this->assertGreaterThan(0, $quickbooksItem['id']);
        $this->assertEquals($application['id'], $quickbooksItem['application_id']);
        $this->assertEquals($container[0]['quickbooks_list_id'], $quickbooksItem['quickbooks_list_id']);
        $this->assertEquals($container[0]['quickbooks_edit_sequence'], $quickbooksItem['quickbooks_edit_sequence']);
    }

    public function providerValidXml()
    {
        // Uncomment the quickbooks-item-query-valid-large.xml element
        // below to run thousands of QuickBooks Items through the Persister.
        $provider = [
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser', 'quickbooks-item-query-valid.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser', 'quickbooks-item-query-valid-discount.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser', 'quickbooks-item-query-valid-fixed-asset.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser', 'quickbooks-item-query-valid-group.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser', 'quickbooks-item-query-valid-inventory.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser', 'quickbooks-item-query-valid-non-inventory.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser', 'quickbooks-item-query-valid-service.xml'],
            //['MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser', 'quickbooks-item-query-valid-large.xml'],
            ['MajorApi\Quickbooks\Parser\Ipp\ItemQueryParser', 'ipp-item-query-response.xml']
        ];

        return $provider;
    }

}
