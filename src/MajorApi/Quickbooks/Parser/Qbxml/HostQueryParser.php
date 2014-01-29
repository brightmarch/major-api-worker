<?php

namespace MajorApi\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\AbstractQbxmlParser;

use \DOMNode;

class HostQueryParser extends AbstractQbxmlParser
{

    public function parse()
    {
        $quickbooksHostQuery = [];

        $hostQueryXpath = $this->xpath
            ->query('//QBXML/QBXMLMsgsRs/HostQueryRs/HostRet')
            ->item(0);

        $isAutomaticLoginString = $this->queryValue('IsAutomaticLogin', $hostQueryXpath);
        $isAutomaticLogin = (self::IS_AUTOMATIC_LOGIN == $isAutomaticLoginString ? true : false);

        $quickbooksHostQuery[] = [
            'quickbooks_product_name' => $this->queryValue('ProductName', $hostQueryXpath),
            'quickbooks_major_version' => $this->queryValue('MajorVersion', $hostQueryXpath),
            'quickbooks_minor_version' => $this->queryValue('MinorVersion', $hostQueryXpath),
            'quickbooks_country' => $this->queryValue('Country', $hostQueryXpath),
            'quickbooks_qb_file_mode' => $this->queryValue('QBFileMode', $hostQueryXpath),
            'quickbooks_supported_qbxml_version' => $this->getSupportedQbxmlVersion($hostQueryXpath),
            'quickbooks_is_automatic_login' => $isAutomaticLogin,
            'quickbooks_is_automatic_login_string' => ($isAutomaticLogin ? 't' : 'f')
        ];

        return $this->appendContainer($quickbooksHostQuery);
    }

    public function getResultTag()
    {
        return 'HostQueryRs';
    }

    private function getSupportedQbxmlVersion(DOMNode $hostQueryXpath)
    {
        // Get the latest supported QBXML Version from the host query response.
        $supportedQbxmlVersion = '';

        $supportedVersionsXpath = $this->xpath
            ->query('SupportedQBXMLVersion', $hostQueryXpath);

        if ($supportedVersionsXpath->length > 0) {
            $index = ($supportedVersionsXpath->length - 1);
            $supportedQbxmlVersion = $supportedVersionsXpath->item($index)->textContent;
        }

        return $supportedQbxmlVersion;
    }

}
