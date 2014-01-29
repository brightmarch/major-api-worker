<?php

namespace MajorApi\Tests\Functional\Quickbooks\Persister;

use MajorApi\Quickbooks\Persister\CustomerQueryPersister;
use MajorApi\Library\Registry;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath,
    \ReflectionClass;

/**
 * @group FunctionalTests
 */
class CustomerQueryPersisterTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testPersistingCustomer($parserClass, $validXml)
    {
        $postgres = Registry::getPostgres();
        $application = $this->getApplicationFixture();

        $xmlPath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlPath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $class = new ReflectionClass($parserClass);
        $parser = $class->newInstance($xml, $dom, $xpath);

        $persister = new CustomerQueryPersister($postgres, $parser, $application['id']);
        $this->assertTrue($persister->persist());

        $container = $persister->getParser()
            ->getContainer();

        $quickbooksCustomer = $postgres->fetchAssoc(
            $persister->getSelectQuery(),
            $persister->getSelectParameters($container[0])
        );

        $this->assertGreaterThan(0, $quickbooksCustomer['id']);
        $this->assertEquals($application['id'], $quickbooksCustomer['application_id']);
        $this->assertEquals($quickbooksCustomer['token'], $application['quickbooks_token']);
        $this->assertEquals($container[0]['quickbooks_list_id'], $quickbooksCustomer['quickbooks_list_id']);
        $this->assertEquals($container[0]['quickbooks_edit_sequence'], $quickbooksCustomer['quickbooks_edit_sequence']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['MajorApi\Quickbooks\Parser\Qbxml\CustomerQueryParser', 'quickbooks-customer-query-valid.xml'],
            ['MajorApi\Quickbooks\Parser\Ipp\CustomerQueryParser', 'ipp-customer-query-response.xml']
        ];

        return $provider;
    }

}
