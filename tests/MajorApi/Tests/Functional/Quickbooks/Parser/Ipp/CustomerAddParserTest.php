<?php

namespace MajorApi\Tests\Functional\Quickbooks\Parser\Ipp;

use MajorApi\Quickbooks\Parser\Ipp\CustomerAddParser;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath;

/**
 * @group FunctionalTests
 */
class CustomerAddParserTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testParsingCustomerAddXml($validXml, $count, $testIndex, $name)
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $parser = new CustomerAddParser($xml, $dom, $xpath);
        $parser->load();
        $parser->initialize();

        $quickbooksCustomers = $parser->parse();

        $this->assertEquals($count, $quickbooksCustomers->count());
        $this->assertEquals($name, $quickbooksCustomers[$testIndex]['name']);
        $this->assertNotEmpty($quickbooksCustomers[$testIndex]['quickbooks_list_id']);
        $this->assertNotEmpty($quickbooksCustomers[$testIndex]['quickbooks_edit_sequence']);
        $this->assertNotEmpty($quickbooksCustomers[$testIndex]['quickbooks_name_token']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['ipp-customer-add-response.xml', 1, 0, 'BILLY JAMES']
        ];

        return $provider;
    }

}
