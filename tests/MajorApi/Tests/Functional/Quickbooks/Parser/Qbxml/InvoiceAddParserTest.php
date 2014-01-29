<?php

namespace MajorApi\Tests\Functional\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\Qbxml\InvoiceAddParser;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath;

/**
 * @group FunctionalTests
 */
class InvoiceAddParserTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testParsingInvoiceAddXml($validXml, $count, $testIndex, $txnId, $refNumber)
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $parser = new InvoiceAddParser($xml, $dom, $xpath);
        $parser->load();
        $parser->initialize();

        $quickbooksInvoices = $parser->parse();

        $this->assertEquals($count, $quickbooksInvoices->count());
        $this->assertEquals($txnId, $quickbooksInvoices[$testIndex]['quickbooks_txn_id']);
        $this->assertEquals($refNumber, $quickbooksInvoices[$testIndex]['ref_number']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['quickbooks-invoice-add-valid-one-invoice.xml', 1, 0, 'C-1357704195', '2'],
            ['quickbooks-invoice-add-valid-two-invoices.xml', 2, 0, 'C-1357704195', '2'],
            ['quickbooks-invoice-add-valid-two-invoices.xml', 2, 1, 'D-1357704195', '6'],
        ];

        return $provider;
    }

}
