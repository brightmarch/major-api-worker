<?php

namespace MajorApi\Tests\Unit\Quickbooks\Parser;

use MajorApi\Quickbooks\Parser\AbstractIppParser;
use MajorApi\Tests\Unit\TestCase;

/**
 * @group UnitTests
 */
class AbstractIppParserTest extends TestCase
{

    public function testQueryingValueRequiresValidQuery()
    {
        $mockDomDocument = $this->getMock('DOMDocument');
        $mockDomNodeList = $this->getMock('DOMNodeList');

        $mockDomXpath = $this->getMockBuilder('DOMXpath')
            ->disableOriginalConstructor()
            ->setMethods(['query'])
            ->getMock();

        $mockDomXpath->expects($this->once())
            ->method('query')
            ->will($this->returnValue($mockDomNodeList));

        $mockDomNode = $this->getMock('DOMNode');

        $parser = $this->getMockForAbstractClass(
            'MajorApi\Quickbooks\Parser\AbstractIppParser',
            ['', $mockDomDocument, $mockDomXpath]
        );

        $this->assertNull($parser->queryValue('', $mockDomNode));
    }

    /**
     * @expectedException MajorApi\Quickbooks\Parser\Exception\Exception
     */
    public function testLoadingXmlRequiresValidXml()
    {
        $mockDomDocument = $this->getMockBuilder('DOMDocument')
            ->setMethods(['loadXML'])
            ->getMock();

        $mockDomDocument->expects($this->once())
            ->method('loadXML')
            ->will($this->returnValue(false));

        $mockDomXpath = $this->getMockBuilder('DOMXpath')
            ->disableOriginalConstructor()
            ->getMock();

        $parser = $this->getMockForAbstractClass(
            'MajorApi\Quickbooks\Parser\AbstractIppParser',
            ['', $mockDomDocument, $mockDomXpath]
        );

        $parser->load();
    }

    public function testInitializingXmlSetsCustomNamespace()
    {
        $mockDomDocument = $this->getMockBuilder('DOMDocument')
            ->setMethods(['lookupNamespaceUri'])
            ->getMock();

        $mockDomDocument->expects($this->once())
            ->method('lookupNamespaceUri')
            ->will($this->returnValue('https://intuit.shit.app.com'));

        $mockDomXpath = $this->getMockBuilder('DOMXpath')
            ->disableOriginalConstructor()
            ->setMethods(['registerNamespace'])
            ->getMock();

        $mockDomXpath->expects($this->once())
            ->method('registerNamespace')
            ->will($this->returnValue(true));

        $parser = $this->getMockForAbstractClass(
            'MajorApi\Quickbooks\Parser\AbstractIppParser',
            ['', $mockDomDocument, $mockDomXpath]
        );

        $parser->initialize();
    }

}
