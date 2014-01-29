<?php

namespace MajorApi\Tests\Functional\Quickbooks\Persister;

use MajorApi\Quickbooks\Persister\AccountQueryPersister;
use MajorApi\Library\Registry;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath,
    \ReflectionClass;

/**
 * @group FunctionalTests
 */
class AccountQueryPersisterTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testPersistingAccount($parserClass, $validXml)
    {
        $postgres = Registry::getPostgres();
        $application = $this->getApplicationFixture();

        $xmlPath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlPath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $class = new ReflectionClass($parserClass);
        $parser = $class->newInstance($xml, $dom, $xpath);

        $persister = new AccountQueryPersister($postgres, $parser, $application['id']);
        $this->assertTrue($persister->persist());

        $container = $persister->getParser()
            ->getContainer();

        $quickbooksAccount = $postgres->fetchAssoc(
            $persister->getSelectQuery(),
            $persister->getSelectParameters($container[0])
        );

        $this->assertGreaterThan(0, $quickbooksAccount['id']);
        $this->assertEquals($application['id'], $quickbooksAccount['application_id']);
        $this->assertEquals($container[0]['account_number'], $quickbooksAccount['account_number']);
        $this->assertEquals($container[0]['quickbooks_list_id'], $quickbooksAccount['quickbooks_list_id']);
        $this->assertEquals($container[0]['quickbooks_edit_sequence'], $quickbooksAccount['quickbooks_edit_sequence']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['MajorApi\Quickbooks\Parser\Qbxml\AccountQueryParser', 'quickbooks-account-query-valid.xml'],
            ['MajorApi\Quickbooks\Parser\Ipp\AccountQueryParser', 'ipp-account-query-response.xml']
        ];

        return $provider;
    }

}
