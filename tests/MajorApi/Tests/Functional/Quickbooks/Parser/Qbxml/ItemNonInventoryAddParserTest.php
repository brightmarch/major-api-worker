<?php

namespace MajorApi\Tests\Functional\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\Qbxml\ItemNonInventoryAddParser;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath;

/**
 * @group FunctionalTests
 */
class ItemNonInventoryAddParserTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testParsingItemNonInventoryAddXml($validXml, $count, $testIndex, $name)
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $parser = new ItemNonInventoryAddParser($xml, $dom, $xpath);
        $parser->load();
        $parser->initialize();

        $quickbooksItems = $parser->parse();

        $this->assertEquals($count, $quickbooksItems->count());
        $this->assertEquals($name, $quickbooksItems[$testIndex]['name']);
        $this->assertNotEmpty($quickbooksItems[$testIndex]['quickbooks_list_id']);
        $this->assertNotEmpty($quickbooksItems[$testIndex]['quickbooks_edit_sequence']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['quickbooks-item-non-inventory-add-valid.xml', 1, 0, 'StripeCharge']
        ];

        return $provider;
    }

}
