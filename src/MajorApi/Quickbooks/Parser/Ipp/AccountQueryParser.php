<?php

namespace MajorApi\Quickbooks\Parser\Ipp;

use MajorApi\Quickbooks\Parser\AbstractIppParser;

class AccountQueryParser extends AbstractIppParser
{

    public function parse()
    {
        $quickbooksAccounts = [];

        $xmlAccounts = $this->xpath->query('//ipp:RestResponse/ipp:Accounts/ipp:Account');

        foreach ($xmlAccounts as $xmlAccount) {
            $name = $this->queryValue('ipp:Name', $xmlAccount);
            $name = strtoupper($name);

            $isActiveString = $this->queryValue('ipp:Active', $xmlAccount);
            $isActive = (self::IS_ACTIVE == $isActiveString ? true : false );

            $quickbooksAccounts[] = [
                'name' => $name,
                'fullname' => $name,
                'is_active' => $isActive,
                'is_active_string' => ($isActive ? 't' : 'f'),
                'sublevel' => 0,
                'type' => $this->queryValue('ipp:Type', $xmlAccount),
                'special_type' => $this->queryValue('ipp:Subtype', $xmlAccount),
                'account_number' => $this->queryValue('ipp:AcctNum', $xmlAccount),
                'bank_number' => null,
                'description' => $this->queryValue('ipp:Desc', $xmlAccount),
                'balance' => (float)$this->queryValue('ipp:CurrentBalance', $xmlAccount),
                'total_balance' => (float)$this->queryValue('ipp:CurrentBalance', $xmlAccount),
                'cash_flow_classification' => null,
                'quickbooks_list_id' => $this->queryValue('ipp:Id', $xmlAccount),
                'quickbooks_edit_sequence' => $this->queryValue('ipp:SyncToken', $xmlAccount),
                'quickbooks_name_token' => md5($name)
            ];
        }

        return $this->appendContainer($quickbooksAccounts);
    }

}
