<?php

namespace MajorApi\Tests\Functional\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\Qbxml\AccountQueryParser;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath;

/**
 * @group FunctionalTests
 */
class AccountQueryParserTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testParsingAccountQueryXml($validXml, $count, $testIndex, $name, $accountNumber, $bankNumber, $balance, $totalBalance)
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $parser = new AccountQueryParser($xml, $dom, $xpath);
        $parser->load();
        $parser->initialize();

        $quickbooksAccounts = $parser->parse();

        $this->assertEquals($count, $quickbooksAccounts->count());
        $this->assertEquals($name, $quickbooksAccounts[$testIndex]['name']);
        $this->assertEquals($accountNumber, $quickbooksAccounts[$testIndex]['account_number']);
        $this->assertEquals($bankNumber, $quickbooksAccounts[$testIndex]['bank_number']);
        $this->assertEquals($balance, $quickbooksAccounts[$testIndex]['balance']);
        $this->assertEquals($totalBalance, $quickbooksAccounts[$testIndex]['total_balance']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['quickbooks-account-query-valid.xml', 1, 0, 'WELLS FARGO', '889976', '88997600001', 29883.56, 156889.29]
        ];

        return $provider;
    }

}
