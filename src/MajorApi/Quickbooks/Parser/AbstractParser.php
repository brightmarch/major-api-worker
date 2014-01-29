<?php

namespace MajorApi\Quickbooks\Parser;

use MajorApi\Quickbooks\Parser\Mixin\ParserMixin;
use MajorApi\Quickbooks\Parser\Exception\Exception;

use \ArrayObject,
    \DateTime,
    \DOMDocument,
    \DOMNode,
    \DOMXpath;

abstract class AbstractParser
{

    use ParserMixin;

    /** @var DOMXpath */
    protected $xpath;

    /** @var DOMNode */
    protected $xmlNode;

    /** @var ArrayObject */
    protected $container;

    /** @var string */
    protected $xml = '';

    /** @const string */
    const IS_ACTIVE = 'true';

    public function __construct($xml, DOMDocument $dom, DOMXpath $xpath)
    {
        $this->xml = $xml;
        $this->dom = $dom;
        $this->xpath = $xpath;
        $this->container = new ArrayObject;
    }

    public function load()
    {
        $loaded = @$this->dom->loadXML($this->xml, LIBXML_NOERROR);

        if (!$loaded) {
            throw new Exception("The XML provided by QuickBooks is not valid XML.");
        }

        // The DOMXpath object is reconstructed with the new DOMDocument
        // object because the DOMXpath object does not create a reference
        // to the DOMDocument object. Because both are injected into this
        // object, the DOMXpath has to be refreshed with the newly loaded
        // DOMDocument object.
        $this->xpath->__construct($this->dom);

        return $this;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getXml()
    {
        return $this->xml;
    }

    abstract public function initialize();
    abstract public function parse();

    protected function appendContainer(array $elements)
    {
        $this->container->exchangeArray($elements);

        return $this->container;
    }

}
