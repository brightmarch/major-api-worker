<?php

namespace MajorApi\Tests\Functional\Quickbooks\Parser;

use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath,
    \ReflectionClass;

/**
 * @group FunctionalTests
 */
class AbstractQbxmlParserTest extends TestCase
{

    /**
     * @expectedException MajorApi\Quickbooks\Parser\Exception\Exception
     * @dataProvider providerParserClass
     */
    public function testLoadingXmlRequiresValidXml($parserClass)
    {
        $xml = '<xml><invalid<xml>here</xml>';

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $class = new ReflectionClass($parserClass);
        $parser = $class->newInstance($xml, $dom, $xpath);

        $parser->load();
    }

    /**
     * @expectedException MajorApi\Quickbooks\Parser\Exception\Exception
     * @dataProvider providerParserClassInvalidResultTagXml
     */
    public function testInitializingParserRequiresResultTag($parserClass, $invalidResultTagXml)
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $invalidResultTagXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $class = new ReflectionClass($parserClass);
        $parser = $class->newInstance($xml, $dom, $xpath);
        $parser->load();

        $parser->initialize();
    }

    /**
     * @expectedException MajorApi\Quickbooks\Parser\Exception\Exception
     * @dataProvider providerParserClassInvalidStatusCodeAttributeXml
     */
    public function testInitializingParserRequiresStatusCodeAttribute($parserClass, $invalidStatusCodeAttributeXml)
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $invalidStatusCodeAttributeXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $class = new ReflectionClass($parserClass);
        $parser = $class->newInstance($xml, $dom, $xpath);
        $parser->load();

        $parser->initialize();
    }

    /**
     * @expectedException MajorApi\Quickbooks\Parser\Exception\Exception
     * @dataProvider providerParserClassInvalidStatusCodeXml
     */
    public function testInitializingParserRequiresOkStatusCode($parserClass, $invalidStatusCodeXml)
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $invalidStatusCodeXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $class = new ReflectionClass($parserClass);
        $parser = $class->newInstance($xml, $dom, $xpath);
        $parser->load();

        $parser->initialize();
    }

    public function providerParserClass()
    {
        $provider = [
            ['MajorApi\Quickbooks\Parser\Qbxml\AccountQueryParser'],
            ['MajorApi\Quickbooks\Parser\Qbxml\CustomerAddParser'],
            ['MajorApi\Quickbooks\Parser\Qbxml\CustomerQueryParser'],
            ['MajorApi\Quickbooks\Parser\Qbxml\HostQueryParser'],
            ['MajorApi\Quickbooks\Parser\Qbxml\InvoiceAddParser'],
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemNonInventoryAddParser'],
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser'],
            ['MajorApi\Quickbooks\Parser\Qbxml\SalesRepQueryParser'],
            ['MajorApi\Quickbooks\Parser\Qbxml\VendorQueryParser']
        ];

        return $provider;
    }

    public function providerParserClassInvalidResultTagXml()
    {
        $provider = [
            ['MajorApi\Quickbooks\Parser\Qbxml\AccountQueryParser', 'quickbooks-account-query-invalid-result-tag.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\CustomerAddParser', 'quickbooks-customer-add-invalid-result-tag.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\CustomerQueryParser', 'quickbooks-customer-query-invalid-result-tag.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\HostQueryParser', 'quickbooks-host-query-invalid-result-tag.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\InvoiceAddParser', 'quickbooks-invoice-add-invalid-result-tag.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemNonInventoryAddParser', 'quickbooks-item-non-inventory-add-invalid-result-tag.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser', 'quickbooks-item-query-invalid-result-tag.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\SalesRepQueryParser', 'quickbooks-sales-rep-query-invalid-result-tag.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\VendorQueryParser', 'quickbooks-vendor-query-invalid-result-tag.xml']
        ];

        return $provider;
    }

    public function providerParserClassInvalidStatusCodeAttributeXml()
    {
        $provider = [
            ['MajorApi\Quickbooks\Parser\Qbxml\AccountQueryParser', 'quickbooks-account-query-invalid-status-code-attribute.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\CustomerAddParser', 'quickbooks-customer-add-invalid-status-code-attribute.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\CustomerQueryParser', 'quickbooks-customer-query-invalid-status-code-attribute.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\HostQueryParser', 'quickbooks-host-query-invalid-status-code-attribute.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\InvoiceAddParser', 'quickbooks-invoice-add-invalid-status-code-attribute.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemNonInventoryAddParser', 'quickbooks-item-non-inventory-add-invalid-status-code-attribute.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser', 'quickbooks-item-query-invalid-status-code-attribute.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\SalesRepQueryParser', 'quickbooks-sales-rep-query-invalid-status-code-attribute.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\VendorQueryParser', 'quickbooks-vendor-query-invalid-status-code-attribute.xml']
        ];

        return $provider;
    }

    public function providerParserClassInvalidStatusCodeXml()
    {
        $provider = [
            ['MajorApi\Quickbooks\Parser\Qbxml\AccountQueryParser', 'quickbooks-account-query-invalid-status-code.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\CustomerAddParser', 'quickbooks-customer-add-invalid-status-code.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\CustomerQueryParser', 'quickbooks-customer-query-invalid-status-code.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\HostQueryParser', 'quickbooks-host-query-invalid-status-code.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\InvoiceAddParser', 'quickbooks-invoice-add-invalid-status-code.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemNonInventoryAddParser', 'quickbooks-item-non-inventory-add-invalid-status-code.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser', 'quickbooks-item-query-invalid-status-code.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\SalesRepQueryParser', 'quickbooks-sales-rep-query-invalid-status-code.xml'],
            ['MajorApi\Quickbooks\Parser\Qbxml\VendorQueryParser', 'quickbooks-vendor-query-invalid-status-code.xml']
        ];

        return $provider;
    }

}
