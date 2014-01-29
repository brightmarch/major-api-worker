<?php

namespace MajorApi\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\AbstractQbxmlParser;

class ItemNonInventoryAddParser extends AbstractQbxmlParser
{

    public function parse()
    {
        $quickbooksItems = [];

        $xmlItems = $this->xpath
            ->query('//QBXML/QBXMLMsgsRs/ItemNonInventoryAddRs/ItemNonInventoryRet');

        foreach ($xmlItems as $xmlItem) {
            $quickbooksItems[] = [
                'name' => $this->queryValue('Name', $xmlItem),
                'quickbooks_list_id' => $this->queryValue('ListID', $xmlItem),
                'quickbooks_edit_sequence' => $this->queryValue('EditSequence', $xmlItem)
            ];
        }

        return $this->appendContainer($quickbooksItems);
    }

    public function getResultTag()
    {
        return 'ItemNonInventoryAddRs';
    }

}
