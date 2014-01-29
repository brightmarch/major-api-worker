<?php

namespace MajorApi\Tests\Functional\Quickbooks\Parser;

use MajorApi\Tests\Functional\TestCase;

use \DOMDocument,
    \DOMXpath,
    \ReflectionClass;

/**
 * @group FunctionalTests
 */
class AbstractIppParserTest extends TestCase
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

    public function providerParserClass()
    {
        $provider = [
            ['MajorApi\Quickbooks\Parser\Ipp\ItemQueryParser']
        ];

        return $provider;
    }

}
