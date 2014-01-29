<?php

namespace MajorApi\Tests\Functional\Quickbooks\Qbxml\Parser\ItemType;

use MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser;
use MajorApi\Quickbooks\Parser\Qbxml\ItemType\InventoryItemType;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath;

/**
 * @group FunctionalTests
 */
class InventoryItemTypeTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testParsingInventoryItemTypeXml($validXml, $name)
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $parser = new ItemQueryParser($xml, $dom, $xpath);
        $parser->load();
        $parser->initialize();

        $quickbooksItems = $parser->parse();

        $this->assertEquals(InventoryItemType::ITEM_TYPE, $quickbooksItems[0]['type']);
        $this->assertEquals($name, $quickbooksItems[0]['name']);
        $this->assertGreaterThan(0, $quickbooksItems[0]['quantity_on_hand']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['quickbooks-item-query-valid-inventory.xml', '32312'],
        ];

        return $provider;
    }

}
