<?php

namespace MajorApi\Tests\Functional\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\Qbxml\VendorQueryParser;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath;

/**
 * @group FunctionalTests
 */
class VendorQueryParserTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testParsingVendorQueryXml($validXml, $count, $testIndex, $contactsCount, $notesCount, $name, $creditLimit, $balance)
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $parser = new VendorQueryParser($xml, $dom, $xpath);
        $parser->load();
        $parser->initialize();

        $quickbooksVendors = $parser->parse();

        $this->assertEquals($count, $quickbooksVendors->count());
        $this->assertEquals($contactsCount, count($quickbooksVendors[$testIndex]['vendor_contacts']));
        $this->assertEquals($notesCount, count($quickbooksVendors[$testIndex]['vendor_notes']));
        $this->assertEquals($name, $quickbooksVendors[$testIndex]['name']);
        $this->assertEquals($creditLimit, $quickbooksVendors[$testIndex]['credit_limit']);
        $this->assertEquals($balance, $quickbooksVendors[$testIndex]['balance']);
        $this->assertContains($quickbooksVendors[$testIndex]['is_active'], [true, false]);
        $this->assertContains($quickbooksVendors[$testIndex]['is_active_string'], ['t', 'f']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['quickbooks-vendor-query-valid.xml', 1, 0, 2, 2, 'APPLE, INC.', 160000, 56899.45],
            ['quickbooks-vendor-query-valid-no-contacts.xml', 1, 0, 0, 2, 'APPLE, INC.', 160000, 56899.45],
            ['quickbooks-vendor-query-valid-no-notes.xml', 1, 0, 2, 0, 'APPLE, INC.', 160000, 56899.45]
        ];

        return $provider;
    }

}
