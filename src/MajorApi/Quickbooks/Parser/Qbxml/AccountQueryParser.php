<?php

namespace MajorApi\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\AbstractQbxmlParser;

class AccountQueryParser extends AbstractQbxmlParser
{

    public function parse()
    {
        $quickbooksAccounts = [];

        $xmlAccounts = $this->xpath
            ->query('//QBXML/QBXMLMsgsRs/AccountQueryRs/AccountRet');

        foreach ($xmlAccounts as $xmlAccount) {
            $name = $this->queryValue('Name', $xmlAccount);
            $name = strtoupper($name);

            $isActiveString = $this->queryValue('IsActive', $xmlAccount);
            $isActive = (self::IS_ACTIVE == $isActiveString ? true : false );

            $quickbooksAccounts[] = [
                'name' => $name,
                'fullname' => $this->queryValue('FullName', $xmlAccount),
                'is_active' => $isActive,
                'is_active_string' => ($isActive ? 't' : 'f'),
                'sublevel' => (int)$this->queryValue('Sublevel', $xmlAccount),
                'type' => $this->queryValue('AccountType', $xmlAccount),
                'special_type' => $this->queryValue('SpecialAccountType', $xmlAccount),
                'account_number' => $this->queryValue('AccountNumber', $xmlAccount),
                'bank_number' => $this->queryValue('BankNumber', $xmlAccount),
                'description' => $this->queryValue('Desc', $xmlAccount),
                'balance' => (float)$this->queryValue('Balance', $xmlAccount),
                'total_balance' => (float)$this->queryValue('TotalBalance', $xmlAccount),
                'cash_flow_classification' => $this->queryValue('CashFlowClassification', $xmlAccount),
                'quickbooks_list_id' => $this->queryValue('ListID', $xmlAccount),
                'quickbooks_edit_sequence' => $this->queryValue('EditSequence', $xmlAccount),
                'quickbooks_name_token' => md5($name)
            ];
        }

        return $this->appendContainer($quickbooksAccounts);
    }

    public function getResultTag()
    {
        return 'AccountQueryRs';
    }

}
