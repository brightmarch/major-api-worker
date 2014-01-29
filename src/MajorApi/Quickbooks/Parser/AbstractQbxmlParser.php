<?php

namespace MajorApi\Quickbooks\Parser;

use MajorApi\Quickbooks\Parser\AbstractParser;
use MajorApi\Quickbooks\Parser\Exception\Exception;

use \DateTime,
    \DOMNode;

abstract class AbstractQbxmlParser extends AbstractParser
{

    /** @const string */
    const STATUS_OK = '0';

    /** @const string */
    const IS_AUTOMATIC_LOGIN = 'true';

    /** @const string */
    const QBXML_TIMEFORMAT = 'Y-m-d\TH:i:sT';

    /**
     * Parses the QBXML to ensure it contains a valid response back from QuickBooks.
     * A valid response has an element like this:
     * <ItemQueryRs statusCode="0" statusSeverity="Info" statusMessage="Status OK">
     * An exception is thrown if an element like that is not found.
     *
     * @return this
     */
    public function initialize()
    {
        $queryRs = $this->xpath
            ->query(sprintf('//QBXML/QBXMLMsgsRs/%s', $this->getResultTag()))
            ->item(0);

        if (!$queryRs) {
            $message = sprintf("The XML provided did not have an <%s> element. This element is required to properly parse QBXML from QuickBooks.", $this->getResultTag());
            throw new Exception($message);
        }

        if (!$queryRs->hasAttribute('statusCode')) {
            $message = sprintf("The <%s> element in the QBXML provided does not have a statusCode attribute.", $this->getResultTag());
            throw new Exception($message);
        }

        $statusCode = $queryRs->getAttribute('statusCode');

        if ($statusCode !== self::STATUS_OK) {
            $message = sprintf("QuickBooks responded with a %s error code when attempting to parse the QBXML returned. The error states: %s", $statusCode, $queryRs->getAttribute('statusMessage'));
            throw new Exception($message);
        }

        return $this;
    }

    abstract public function getResultTag();

    protected function getDate($field, DOMNode $xmlNode)
    {
        $date = DateTime::createFromFormat(self::QBXML_TIMEFORMAT, $this->queryValue($field, $xmlNode));

        if (!$date) {
            $date = new DateTime;
        }

        return $date;
    }

}
