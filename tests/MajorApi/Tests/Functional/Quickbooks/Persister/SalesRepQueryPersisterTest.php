<?php

namespace MajorApi\Tests\Functional\Quickbooks\Persister;

use MajorApi\Quickbooks\Parser\Qbxml\SalesRepQueryParser;
use MajorApi\Quickbooks\Persister\SalesRepQueryPersister;
use MajorApi\Library\Registry;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath,
    \ReflectionClass;

/**
 * @group FunctionalTests
 */
class SalesRepQueryPersisterTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testPersistingSalesRep($parserClass, $validXml)
    {
        $postgres = Registry::getPostgres();
        $application = $this->getApplicationFixture();

        $xmlPath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlPath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $class = new ReflectionClass($parserClass);
        $parser = $class->newInstance($xml, $dom, $xpath);

        $persister = new SalesRepQueryPersister($postgres, $parser, $application['id']);
        $this->assertTrue($persister->persist());

        $container = $persister->getParser()
            ->getContainer();

        $quickbooksSalesRep = $postgres->fetchAssoc(
            $persister->getSelectQuery(),
            $persister->getSelectParameters($container[0])
        );

        $this->assertGreaterThan(0, $quickbooksSalesRep['id']);
        $this->assertContains($quickbooksSalesRep['is_active'], [true, false]);
    }

    public function testPersistingUpdatedSalesRep()
    {
        $application = $this->getApplicationFixture();

        $xmlPath = $this->fixtureDir . '/quickbooks-sales-rep-query-valid.xml';
        $xml = file_get_contents($xmlPath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $postgres = Registry::getPostgres();
        $parser = new SalesRepQueryParser($xml, $dom, $xpath);

        $persister = new SalesRepQueryPersister($postgres, $parser, $application['id']);
        $persister->persist();

        $xmlPath = $this->fixtureDir . '/quickbooks-sales-rep-query-valid-updated.xml';
        $xml = file_get_contents($xmlPath);

        $parser = new SalesRepQueryParser($xml, $dom, $xpath);

        $persister = new SalesRepQueryPersister($postgres, $parser, $application['id']);
        $this->assertTrue($persister->persist());

        $container = $persister->getParser()
            ->getContainer();

        $quickbooksSalesRep = $postgres->fetchAssoc(
            $persister->getSelectQuery(),
            $persister->getSelectParameters($container[0])
        );

        $this->assertGreaterThan(0, $quickbooksSalesRep['id']);
        $this->assertFalse($quickbooksSalesRep['is_active']);
    }

    public function providerValidXml()
    {
        // Uncomment the quickbooks-item-query-valid-large.xml element
        // below to run thousands of QuickBooks Items through the Persister.
        $provider = [
            ['MajorApi\Quickbooks\Parser\Qbxml\SalesRepQueryParser', 'quickbooks-sales-rep-query-valid.xml'],
            ['MajorApi\Quickbooks\Parser\Ipp\SalesRepQueryParser', 'ipp-sales-rep-query-response.xml']
        ];

        return $provider;
    }

}
