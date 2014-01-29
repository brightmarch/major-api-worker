<?php

namespace MajorApi\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\AbstractQbxmlParser;

class SalesRepQueryParser extends AbstractQbxmlParser
{

    public function parse()
    {
        $quickbooksSalesReps = [];

        $xmlSalesReps = $this->xpath
            ->query('//QBXML/QBXMLMsgsRs/SalesRepQueryRs/SalesRepRet');

        foreach ($xmlSalesReps as $xmlSalesRep) {
            $isActiveString = $this->queryValue('IsActive', $xmlSalesRep);
            $isActive = (self::IS_ACTIVE == $isActiveString ? true : false );

            $quickbooksSalesReps[] = [
                'initial' => $this->queryValue('Initial', $xmlSalesRep),
                'is_active' => $isActive,
                'is_active_string' => ($isActive ? 't' : 'f'),
                'quickbooks_list_id' => $this->queryValue('ListID', $xmlSalesRep),
                'quickbooks_edit_sequence' => $this->queryValue('EditSequence', $xmlSalesRep)
            ];
        }

        return $this->appendContainer($quickbooksSalesReps);
    }

    public function getResultTag()
    {
        return 'SalesRepQueryRs';
    }

}
