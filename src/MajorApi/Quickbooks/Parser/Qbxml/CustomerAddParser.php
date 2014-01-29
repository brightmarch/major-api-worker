<?php

namespace MajorApi\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\AbstractQbxmlParser;

class CustomerAddParser extends AbstractQbxmlParser
{

    public function parse()
    {
        $quickbooksCustomers = [];

        $xmlCustomers = $this->xpath
            ->query('//QBXML/QBXMLMsgsRs/CustomerAddRs/CustomerRet');

        foreach ($xmlCustomers as $xmlCustomer) {
            $name = $this->queryValue('Name', $xmlCustomer);
            $name = strtoupper($name);

            $quickbooksCustomers[] = [
                'name' => $name,
                'quickbooks_list_id' => $this->queryValue('ListID', $xmlCustomer),
                'quickbooks_edit_sequence' => $this->queryValue('EditSequence', $xmlCustomer),
                'quickbooks_name_token' => md5($name)
            ];
        }

        return $this->appendContainer($quickbooksCustomers);
    }

    public function getResultTag()
    {
        return 'CustomerAddRs';
    }

}
