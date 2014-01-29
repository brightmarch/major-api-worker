<?php

namespace MajorApi\Quickbooks\Parser\Ipp;

use MajorApi\Quickbooks\Parser\AbstractIppParser;

class SalesRepQueryParser extends AbstractIppParser
{

    public function parse()
    {
        $quickbooksSalesReps = [];

        $xmlSalesReps = $this->xpath->query('//ipp:RestResponse/ipp:SalesReps/ipp:SalesRep');

        foreach ($xmlSalesReps as $xmlSalesRep) {
            $isActive = true;

            $quickbooksSalesReps[] = [
                'initial' => $this->queryValue('ipp:Initials', $xmlSalesRep),
                'is_active' => $isActive,
                'is_active_string' => ($isActive ? 't' : 'f'),
                'quickbooks_list_id' => $this->queryValue('ipp:Id', $xmlSalesRep),
                'quickbooks_edit_sequence' => $this->queryValue('ipp:SyncToken', $xmlSalesRep)
            ];
        }

        return $this->appendContainer($quickbooksSalesReps);
    }

}
