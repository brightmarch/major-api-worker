<?php

namespace MajorApi\Tests\Functional\Quickbooks\Persister;

use MajorApi\Quickbooks\Persister\CustomerAddPersister;
use MajorApi\Library\Registry;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath,
    \ReflectionClass;

/**
 * @group FunctionalTests
 */
class CustomerAddPersisterTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testPersistingUpdatedCustomer($parserClass, $validXml)
    {
        $postgres = Registry::getPostgres();
        $application = $this->getApplicationFixture();

        $xmlPath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlPath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $class = new ReflectionClass($parserClass);
        $parser = $class->newInstance($xml, $dom, $xpath);

        $container = $parser->load()
            ->initialize()
            ->parse();

        // Create a customer that will be modified by the CustomerAddPersister.
        $quickbooksCustomer = [
            'created' => Registry::getTimeString(),
            'updated' => Registry::getTimeString(),
            'status' => Registry::STATUS_ENABLED,
            'application_id' => $application['id'],
            'name' => $container[0]['quickbooks_name_token'],
            'quickbooks_name_token' => $container[0]['quickbooks_name_token']
        ];

        $postgres->insert('api_quickbooks_customer', $quickbooksCustomer);

        // Persist the QuickBooks CustomerAdd results
        $persister = new CustomerAddPersister($postgres, $parser, $application['id']);
        $this->assertTrue($persister->persist());

        $quickbooksCustomer = $postgres->fetchAssoc(
            $persister->getSelectQuery(),
            $persister->getSelectParameters($container[0])
        );

        $this->assertEquals($application['quickbooks_token'], $quickbooksCustomer['token']);
        $this->assertEquals($container[0]['quickbooks_list_id'], $quickbooksCustomer['quickbooks_list_id']);
        $this->assertEquals($container[0]['quickbooks_edit_sequence'], $quickbooksCustomer['quickbooks_edit_sequence']);
    }

    /**
     * @dataProvider providerValidXml
     */
    public function testPersistingInvalidCustomer($parserClass, $validXml)
    {
        $postgres = Registry::getPostgres();
        $application = $this->getApplicationFixture();

        $xmlPath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlPath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $class = new ReflectionClass($parserClass);
        $parser = $class->newInstance($xml, $dom, $xpath);

        $persister = new CustomerAddPersister($postgres, $parser, $application['id']);

        $this->assertTrue($persister->persist());
    }

    public function providerValidXml()
    {
        $provider = [
            ['MajorApi\Quickbooks\Parser\Qbxml\CustomerAddParser', 'quickbooks-customer-add-valid.xml'],
            ['MajorApi\Quickbooks\Parser\Ipp\CustomerAddParser', 'ipp-customer-add-response.xml']
        ];

        return $provider;
    }

}
