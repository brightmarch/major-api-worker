<?php

namespace MajorApi\Tests\Functional\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\Qbxml\SalesRepQueryParser;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath;

/**
 * @group FunctionalTests
 */
class SalesRepQueryParserTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testParsingSalesRepQueryXml($validXml, $count, $testIndex, $initial)
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $parser = new SalesRepQueryParser($xml, $dom, $xpath);
        $parser->load();
        $parser->initialize();

        $quickbooksSalesReps = $parser->parse();

        $this->assertEquals($count, $quickbooksSalesReps->count());
        $this->assertEquals($initial, $quickbooksSalesReps[$testIndex]['initial']);
        $this->assertContains($quickbooksSalesReps[$testIndex]['is_active'], [true, false]);
        $this->assertContains($quickbooksSalesReps[$testIndex]['is_active_string'], ['t', 'f']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['quickbooks-sales-rep-query-valid.xml', 1, 0, 'VMC']
        ];

        return $provider;
    }

}
