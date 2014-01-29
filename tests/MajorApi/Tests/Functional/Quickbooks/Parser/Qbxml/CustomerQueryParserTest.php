<?php

namespace MajorApi\Tests\Functional\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\Qbxml\CustomerQueryParser;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath;

/**
 * @group FunctionalTests
 */
class CustomerQueryParserTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testParsingCustomerQueryXml($validXml, $count, $testIndex, $name)
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $parser = new CustomerQueryParser($xml, $dom, $xpath);
        $parser->load();
        $parser->initialize();

        $quickbooksCustomers = $parser->parse();

        $this->assertEquals($count, $quickbooksCustomers->count());
        $this->assertEquals($name, $quickbooksCustomers[$testIndex]['name']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['quickbooks-customer-query-valid.xml', 1, 0, 'VIC CHERUBINI']
        ];

        return $provider;
    }

}
