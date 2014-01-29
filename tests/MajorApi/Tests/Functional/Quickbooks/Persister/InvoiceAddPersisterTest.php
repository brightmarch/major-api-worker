<?php

namespace MajorApi\Tests\Functional\Quickbooks\Persister;

use MajorApi\Quickbooks\Parser\Qbxml\CustomerQueryParser;
use MajorApi\Quickbooks\Parser\Qbxml\InvoiceAddParser;
use MajorApi\Quickbooks\Persister\InvoiceAddPersister;
use MajorApi\Library\Registry;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath;

/**
 * @group FunctionalTests
 */
class InvoiceAddPersisterTest extends TestCase
{

    public function testPersistingUpdatedInvoice()
    {
        $postgres = Registry::getPostgres();
        $application = $this->getApplicationFixture();

        // Create a new QuickBooks Customer that can be bound to the QuickBooks Invoice.
        $xmlPath = $this->fixtureDir . '/quickbooks-customer-query-valid.xml';
        $xml = file_get_contents($xmlPath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $parser = new CustomerQueryParser($xml, $dom, $xpath);
        $container = $parser->load()
            ->initialize()
            ->parse();

        $quickbooksCustomer = [
            'created' => Registry::getTimeString(),
            'updated' => Registry::getTimeString(),
            'status' => Registry::STATUS_ENABLED,
            'application_id' => $application['id'],
            'name' => $container[0]['name'],
            'quickbooks_name_token' => $container[0]['quickbooks_name_token']
        ];

        $postgres->insert('api_quickbooks_customer', $quickbooksCustomer);
        $quickbooksCustomerId = $postgres->lastInsertId('api_quickbooks_customer_id_seq');

        // Create a new QuickBooks Invoice that will be updated by the Persister.
        $xmlPath = $this->fixtureDir . '/quickbooks-invoice-add-valid-one-invoice.xml';
        $xml = file_get_contents($xmlPath);

        $parser = new InvoiceAddParser($xml, $dom, $xpath);
        $container = $parser->load()
            ->initialize()
            ->parse();

        $quickbooksInvoice = [
            'created' => Registry::getTimeString(),
            'updated' => Registry::getTimeString(),
            'status' => Registry::STATUS_ENABLED,
            'application_id' => $application['id'],
            'quickbooks_customer_id' => $quickbooksCustomerId,
            'ref_number' => $container[0]['ref_number'],
            'invoice_date' => Registry::getTimeString()
        ];

        $postgres->insert('api_quickbooks_invoice', $quickbooksInvoice);

        $persister = new InvoiceAddPersister($postgres, $parser, $application['id']);
        $this->assertTrue($persister->persist());

        $quickbooksInvoice = $postgres->fetchAssoc(
            $persister->getSelectQuery(),
            $persister->getSelectParameters($container[0])
        );

        $this->assertEquals($application['quickbooks_token'], $quickbooksInvoice['token']);
        $this->assertEquals($container[0]['quickbooks_txn_id'], $quickbooksInvoice['quickbooks_txn_id']);
        $this->assertEquals($container[0]['quickbooks_txn_number'], $quickbooksInvoice['quickbooks_txn_number']);
        $this->assertEquals($container[0]['quickbooks_edit_sequence'], $quickbooksInvoice['quickbooks_edit_sequence']);
    }

    public function testPersistingInvalidInvoice()
    {
        $postgres = Registry::getPostgres();
        $application = $this->getApplicationFixture();

        $xmlPath = $this->fixtureDir . '/quickbooks-invoice-add-valid-one-invoice.xml';
        $xml = file_get_contents($xmlPath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $parser = new InvoiceAddParser($xml, $dom, $xpath);
        $persister = new InvoiceAddPersister($postgres, $parser, $application['id']);

        $this->assertTrue($persister->persist());
    }

}
