<?php

namespace MajorApi\Tests\Functional\Quickbooks\Parser\Qbxml\ItemType;

use MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser;
use MajorApi\Quickbooks\Parser\Qbxml\ItemType\GroupItemType;
use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath;

/**
 * @group FunctionalTests
 */
class GroupItemTypeTest extends TestCase
{

    /**
     * @dataProvider providerValidXml
     */
    public function testParsingGroupItemTypeXml($validXml, $name)
    {
        $xmlFilePath = sprintf('%s/%s', $this->fixtureDir, $validXml);
        $xml = file_get_contents($xmlFilePath);

        $dom = new DOMDocument;
        $xpath = new DOMXpath($dom);

        $parser = new ItemQueryParser($xml, $dom, $xpath);
        $parser->load();
        $parser->initialize();

        $quickbooksItems = $parser->parse();

        $this->assertEquals(GroupItemType::ITEM_TYPE, $quickbooksItems[0]['type']);
        $this->assertEquals($name, $quickbooksItems[0]['name']);
    }

    public function providerValidXml()
    {
        $provider = [
            ['quickbooks-item-query-valid-group.xml', 'ADA1045'],
        ];

        return $provider;
    }

}
