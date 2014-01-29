<?php

namespace MajorApi\Tests\Functional\Quickbooks\Parser\Ipp;

use MajorApi\Quickbooks\Parser\Ipp\CustomerQueryParser;
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
            ['ipp-customer-query-response.xml', 8, 0, 'JOE SMITH'],
            ['ipp-customer-query-response.xml', 8, 1, 'EPIC SOFTWARE']
        ];

        return $provider;
    }

}
