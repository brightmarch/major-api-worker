<?php

namespace MajorApi\Tests\Functional\Quickbooks\Parser\Ipp;

use MajorApi\Quickbooks\Parser\Ipp\ItemQueryParser;
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
            ['ipp-item-query-response.xml', 4, 0, 'BMPencil', 6.98],
            ['ipp-item-query-response.xml', 4, 1, 'BMServicePro', 1594.33],
            ['ipp-item-query-response.xml', 4, 2, 'BMArtDesign', 150],
            ['ipp-item-query-response.xml', 4, 3, 'BMRate', 100],
        ];

        return $provider;
    }

}
