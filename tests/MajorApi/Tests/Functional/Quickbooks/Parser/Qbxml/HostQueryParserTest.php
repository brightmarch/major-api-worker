<?php

namespace MajorApi\Tests\Functional\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\Qbxml\HostQueryParser;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath;

/**
 * @group FunctionalTests
 */
class HostQueryParserTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testParsingHostQueryXml(
        $validXml,
        $count,
        $testIndex,
        $majorVersion,
        $country,
        $supportedQbxmlVersion
    )
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $parser = new HostQueryParser($xml, $dom, $xpath);
        $parser->load();
        $parser->initialize();

        $quickbooksHost = $parser->parse();

        $this->assertEquals($count, $quickbooksHost->count());
        $this->assertEquals($majorVersion, $quickbooksHost[$testIndex]['quickbooks_major_version']);
        $this->assertEquals($country, $quickbooksHost[$testIndex]['quickbooks_country']);
        $this->assertEquals($supportedQbxmlVersion, $quickbooksHost[$testIndex]['quickbooks_supported_qbxml_version']);
        $this->assertContains($quickbooksHost[$testIndex]['quickbooks_is_automatic_login_string'], ['t', 'f']);
        $this->assertNotEquals($quickbooksHost[$testIndex]['quickbooks_major_version'], $quickbooksHost[$testIndex]['quickbooks_minor_version']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['quickbooks-host-query-valid.xml', 1, 0, '23', 'US', '12.0']
        ];

        return $provider;
    }

}
