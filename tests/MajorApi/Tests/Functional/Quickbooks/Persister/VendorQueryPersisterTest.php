<?php

namespace MajorApi\Tests\Functional\Quickbooks\Persister;

use MajorApi\Quickbooks\Persister\VendorQueryPersister;
use MajorApi\Library\Registry;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath,
    \ReflectionClass;

/**
 * @group FunctionalTests
 */
class VendorQueryPersisterTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testPersistingVendor($parserClass, $validXml)
    {
        $postgres = Registry::getPostgres();
        $application = $this->getApplicationFixture();

        $xmlPath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlPath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $class = new ReflectionClass($parserClass);
        $parser = $class->newInstance($xml, $dom, $xpath);

        $persister = new VendorQueryPersister($postgres, $parser, $application['id']);
        $this->assertTrue($persister->persist());

        $container = $persister->getParser()
            ->getContainer();

        $quickbooksVendor = $postgres->fetchAssoc(
            $persister->getSelectQuery(),
            $persister->getSelectParameters($container[0])
        );

        $this->assertGreaterThan(0, $quickbooksVendor['id']);
        $this->assertEquals($application['id'], $quickbooksVendor['application_id']);
        $this->assertEquals($quickbooksVendor['token'], $application['quickbooks_token']);
        $this->assertEquals($container[0]['quickbooks_list_id'], $quickbooksVendor['quickbooks_list_id']);
        $this->assertEquals($container[0]['quickbooks_edit_sequence'], $quickbooksVendor['quickbooks_edit_sequence']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['MajorApi\Quickbooks\Parser\Qbxml\VendorQueryParser', 'quickbooks-vendor-query-valid.xml'],
            ['MajorApi\Quickbooks\Parser\Ipp\VendorQueryParser', 'ipp-vendor-query-response.xml']
        ];

        return $provider;
    }

}
