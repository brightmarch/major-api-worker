<?php

namespace MajorApi\Tests\Functional\Quickbooks\Parser\Qbxml\ItemType;

use MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser;
use MajorApi\Quickbooks\Parser\Qbxml\ItemType\DiscountItemType;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath;

/**
 * @group FunctionalTests
 */
class DiscountItemTypeTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testParsingDiscountItemTypeXml($validXml, $name)
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $parser = new ItemQueryParser($xml, $dom, $xpath);
        $parser->load();
        $parser->initialize();

        $quickbooksItems = $parser->parse();

        $this->assertEquals(DiscountItemType::ITEM_TYPE, $quickbooksItems[0]['type']);
        $this->assertEquals($name, $quickbooksItems[0]['name']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['quickbooks-item-query-valid-discount.xml', 'ADD'],
        ];

        return $provider;
    }

}
