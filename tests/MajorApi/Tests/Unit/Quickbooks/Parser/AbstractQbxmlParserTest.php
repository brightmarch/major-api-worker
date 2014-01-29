<?php

namespace MajorApi\Tests\Unit\Quickbooks\Parser;

use MajorApi\Quickbooks\Parser\AbstractQbxmlParser;
use MajorApi\Tests\Unit\TestCase;

/**
 * @group UnitTests
 */
class AbstractQbxmlParserTest extends TestCase
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
            'MajorApi\Quickbooks\Parser\AbstractQbxmlParser',
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
            'MajorApi\Quickbooks\Parser\AbstractQbxmlParser',
            ['', $mockDomDocument, $mockDomXpath]
        );

        $parser->load();
    }

    /**
     * @expectedException MajorApi\Quickbooks\Parser\Exception\Exception
     */
    public function testInitializingXmlRequiresResultTag()
    {
        $mockDomDocument = $this->getMock('DOMDocument');

        $mockDomNodeList = $this->getMockBuilder('DOMNodeList')
            ->setMethods(['item'])
            ->getMock();

        $mockDomNodeList->expects($this->once())
            ->method('item')
            ->will($this->returnValue(false));

        $mockDomXpath = $this->getMockBuilder('DOMXpath')
            ->disableOriginalConstructor()
            ->setMethods(['query'])
            ->getMock();

        $mockDomXpath->expects($this->once())
            ->method('query')
            ->will($this->returnValue($mockDomNodeList));

        $parser = $this->getMockForAbstractClass(
            'MajorApi\Quickbooks\Parser\AbstractQbxmlParser',
            ['', $mockDomDocument, $mockDomXpath]
        );

        $parser->initialize();
    }

    /**
     * @expectedException MajorApi\Quickbooks\Parser\Exception\Exception
     */
    public function testInitializingParserRequiresStatusCodeAttribute()
    {
        $mockDomDocument = $this->getMock('DOMDocument');

        $mockDomNode = $this->getMockBuilder('DOMNode')
            ->setMethods(['hasAttribute'])
            ->getMock();

        $mockDomNode->expects($this->once())
            ->method('hasAttribute')
            ->with($this->equalTo('statusCode'))
            ->will($this->returnValue(false));

        $mockDomNodeList = $this->getMockBuilder('DOMNodeList')
            ->setMethods(['item'])
            ->getMock();

        $mockDomNodeList->expects($this->once())
            ->method('item')
            ->will($this->returnValue($mockDomNode));

        $mockDomXpath = $this->getMockBuilder('DOMXpath')
            ->disableOriginalConstructor()
            ->setMethods(['query'])
            ->getMock();

        $mockDomXpath->expects($this->once())
            ->method('query')
            ->will($this->returnValue($mockDomNodeList));

        $parser = $this->getMockForAbstractClass(
            'MajorApi\Quickbooks\Parser\AbstractQbxmlParser',
            ['', $mockDomDocument, $mockDomXpath]
        );

        $parser->initialize();
    }

    /**
     * @expectedException MajorApi\Quickbooks\Parser\Exception\Exception
     */
    public function testInitializingParserRequiresOkStatusCode()
    {
        $mockDomDocument = $this->getMock('DOMDocument');

        $mockDomNode = $this->getMockBuilder('DOMNode')
            ->setMethods(['hasAttribute', 'getAttribute'])
            ->getMock();

        $mockDomNode->expects($this->once())
            ->method('hasAttribute')
            ->with($this->equalTo('statusCode'))
            ->will($this->returnValue(true));

        $mockDomNode->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnValue('invalid'));

        $mockDomNodeList = $this->getMockBuilder('DOMNodeList')
            ->setMethods(['item'])
            ->getMock();

        $mockDomNodeList->expects($this->once())
            ->method('item')
            ->will($this->returnValue($mockDomNode));

        $mockDomXpath = $this->getMockBuilder('DOMXpath')
            ->disableOriginalConstructor()
            ->setMethods(['query'])
            ->getMock();

        $mockDomXpath->expects($this->once())
            ->method('query')
            ->will($this->returnValue($mockDomNodeList));

        $parser = $this->getMockForAbstractClass(
            'MajorApi\Quickbooks\Parser\AbstractQbxmlParser',
            ['', $mockDomDocument, $mockDomXpath]
        );

        $parser->initialize();
    }

}
