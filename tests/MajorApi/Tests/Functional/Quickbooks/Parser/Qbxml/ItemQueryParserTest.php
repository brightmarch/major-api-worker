<?php

namespace MajorApi\Tests\Functional\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath;

/**
 * @group FunctionalTests
 */
class ItemQueryParserTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testParsingItemQueryXml($validXml, $count, $testIndex, $name, $price)
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $parser = new ItemQueryParser($xml, $dom, $xpath);
        $parser->load();
        $parser->initialize();

        $quickbooksItems = $parser->parse();

        $this->assertEquals($count, $quickbooksItems->count());
        $this->assertEquals($name, $quickbooksItems[$testIndex]['name']);
        $this->assertEquals($price, $quickbooksItems[$testIndex]['price']);
        $this->assertNotEmpty($quickbooksItems[$testIndex]['description']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['quickbooks-item-query-valid.xml', 2, 0, 'BMServiceFee', 39.00],
            ['quickbooks-item-query-valid.xml', 2, 1, 'Labor', 212.50],
        ];

        return $provider;
    }

}
