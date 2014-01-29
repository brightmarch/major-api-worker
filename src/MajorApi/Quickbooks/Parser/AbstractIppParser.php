<?php

namespace MajorApi\Quickbooks\Parser;

use MajorApi\Quickbooks\Parser\AbstractParser;
use MajorApi\Quickbooks\Parser\Exception\Exception;

use \DateTime,
    \DOMNode;

abstract class AbstractIppParser extends AbstractParser
{

    /** @const string */
    const IPP_TIMEFORMAT = 'Y-m-d\TH:i:sT';

    public function initialize()
    {
        // We are guaranteed to have a valid response because an error response is not
        // even sent to the parser by the command class. Thus, just register the
        // namespace and be done with it.
        $namespaceUri = $this->dom->lookupNamespaceUri($this->dom->namespaceURI);
        if (!empty($namespaceUri)) {
            $this->xpath->registerNamespace('ipp', $namespaceUri);
        }

        return $this;
    }

    protected function getDate($field, DOMNode $xmlNode)
    {
        $date = DateTime::createFromFormat(self::IPP_TIMEFORMAT, $this->queryValue($field, $xmlNode));

        if (!$date) {
            $date = new DateTime;
        }

        return $date;
    }

}
