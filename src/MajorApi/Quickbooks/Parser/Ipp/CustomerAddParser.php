<?php

namespace MajorApi\Quickbooks\Parser\Ipp;

use MajorApi\Quickbooks\Parser\AbstractIppParser;

class CustomerAddParser extends AbstractIppParser
{

    public function parse()
    {
        $quickbooksCustomers = [];

        $xmlCustomers = $this->xpath->query('//ipp:RestResponse/ipp:Success/ipp:Customer');

        foreach ($xmlCustomers as $xmlCustomer) {
            $name = $this->queryValue('ipp:Name', $xmlCustomer);
            $name = strtoupper($name);

            $quickbooksCustomers[] = [
                'name' => $name,
                'quickbooks_list_id' => $this->queryValue('ipp:Id', $xmlCustomer),
                'quickbooks_edit_sequence' => $this->queryValue('ipp:SyncToken', $xmlCustomer),
                'quickbooks_name_token' => md5($name)
            ];
        }

        return $this->appendContainer($quickbooksCustomers);
    }

}
