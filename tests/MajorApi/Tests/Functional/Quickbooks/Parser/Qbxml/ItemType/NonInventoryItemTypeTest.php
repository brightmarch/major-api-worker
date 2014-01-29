<?php

namespace MajorApi\Tests\Functional\Quickbooks\Qbxml\Parser\ItemType;

use MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser;
use MajorApi\Quickbooks\Parser\Qbxml\ItemType\NonInventoryItemType;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath;

/**
 * @group FunctionalTests
 */
class NonInventoryItemTypeTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testParsingNonInventoryItemTypeXml($validXml, $name)
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $parser = new ItemQueryParser($xml, $dom, $xpath);
        $parser->load();
        $parser->initialize();

        $quickbooksItems = $parser->parse();

        $this->assertEquals(NonInventoryItemType::ITEM_TYPE, $quickbooksItems[0]['type']);
        $this->assertEquals($name, $quickbooksItems[0]['name']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['quickbooks-item-query-valid-non-inventory.xml', '32312'],
        ];

        return $provider;
    }

}
