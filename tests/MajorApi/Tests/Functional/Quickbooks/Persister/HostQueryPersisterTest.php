<?php

namespace MajorApi\Tests\Functional\Quickbooks\Persister;

use MajorApi\Quickbooks\Parser\Qbxml\HostQueryParser;
use MajorApi\Quickbooks\Persister\HostQueryPersister;
use MajorApi\Library\Registry;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath;

/**
 * @group FunctionalTests
 */
class HostQueryPersisterTest extends TestCase
{

    public function testPersistingHost()
    {
        $postgres = Registry::getPostgres();
        $application = $this->getApplicationFixture();

        $xmlPath = $this->fixtureDir . '/quickbooks-host-query-valid.xml';
        $xml = file_get_contents($xmlPath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);
        $parser = new HostQueryParser($xml, $dom, $xpath);

        $persister = new HostQueryPersister($postgres, $parser, $application['id']);
        $this->assertTrue($persister->persist());

        $container = $persister->getParser()
            ->getContainer();

        $application = $postgres->fetchAssoc(
            $persister->getSelectQuery(),
            $persister->getSelectParameters($container[0])
        );

        $this->assertEquals($container[0]['quickbooks_product_name'], $application['quickbooks_product_name']);
        $this->assertEquals($container[0]['quickbooks_minor_version'], $application['quickbooks_minor_version']);
        $this->assertNotEquals($application['quickbooks_major_version'], $application['quickbooks_minor_version']);
        $this->assertContains($application['quickbooks_is_automatic_login'], [true, false]);
        $this->assertEquals($container[0]['quickbooks_supported_qbxml_version'], $application['quickbooks_supported_qbxml_version']);
    }

}
