<?php

namespace MajorApi\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\AbstractQbxmlParser;

class CustomerQueryParser extends AbstractQbxmlParser
{

    public function parse()
    {
        $quickbooksCustomers = [];

        $xmlCustomers = $this->xpath
            ->query('//QBXML/QBXMLMsgsRs/CustomerQueryRs/CustomerRet');

        foreach ($xmlCustomers as $xmlCustomer) {
            $name = $this->queryValue('Name', $xmlCustomer);
            $name = strtoupper($name);

            $isActiveString = $this->queryValue('IsActive', $xmlCustomer);
            $isActive = (self::IS_ACTIVE == $isActiveString ? true : false );

            $quickbooksCustomers[] = [
                'name' => $name,
                'is_active' => $isActive,
                'is_active_string' => ($isActive ? 't' : 'f'),
                'company_name' => $this->queryValue('CompanyName', $xmlCustomer),
                'salutation' => $this->queryValue('Salutation', $xmlCustomer),
                'first_name' => $this->queryValue('FirstName', $xmlCustomer),
                'middle_name' => $this->queryValue('MiddleName', $xmlCustomer),
                'last_name' => $this->queryValue('LastName', $xmlCustomer),
                'job_title' => $this->queryValue('JobTitle', $xmlCustomer),
                'bill_address1' => $this->queryValue('BillAddress/Addr1', $xmlCustomer),
                'bill_address2' => $this->queryValue('BillAddress/Addr2', $xmlCustomer),
                'bill_address3' => $this->queryValue('BillAddress/Addr3', $xmlCustomer),
                'bill_address4' => $this->queryValue('BillAddress/Addr4', $xmlCustomer),
                'bill_address5' => $this->queryValue('BillAddress/Addr5', $xmlCustomer),
                'bill_city' => $this->queryValue('BillAddress/City', $xmlCustomer),
                'bill_state' => $this->queryValue('BillAddress/State', $xmlCustomer),
                'bill_postal_code' => $this->queryValue('BillAddress/PostalCode', $xmlCustomer),
                'bill_country' => $this->queryValue('BillAddress/Country', $xmlCustomer),
                'bill_note' => $this->queryValue('BillAddress/Note', $xmlCustomer),
                'ship_address1' => $this->queryValue('ShipAddress/Addr1', $xmlCustomer),
                'ship_address2' => $this->queryValue('ShipAddress/Addr2', $xmlCustomer),
                'ship_address3' => $this->queryValue('ShipAddress/Addr3', $xmlCustomer),
                'ship_address4' => $this->queryValue('ShipAddress/Addr4', $xmlCustomer),
                'ship_address5' => $this->queryValue('ShipAddress/Addr5', $xmlCustomer),
                'ship_city' => $this->queryValue('ShipAddress/City', $xmlCustomer),
                'ship_state' => $this->queryValue('ShipAddress/State', $xmlCustomer),
                'ship_postal_code' => $this->queryValue('ShipAddress/PostalCode', $xmlCustomer),
                'ship_country' => $this->queryValue('ShipAddress/Country', $xmlCustomer),
                'ship_note' => $this->queryValue('ShipAddress/Note', $xmlCustomer),
                'phone' => $this->queryValue('Phone', $xmlCustomer),
                'alt_phone' => $this->queryValue('AltPhone', $xmlCustomer),
                'fax' => $this->queryValue('Fax', $xmlCustomer),
                'email' => $this->queryValue('Email', $xmlCustomer),
                'email_cc' => $this->queryValue('Cc', $xmlCustomer),
                'notes' => $this->queryValue('Notes', $xmlCustomer),
                'quickbooks_list_id' => $this->queryValue('ListID', $xmlCustomer),
                'quickbooks_edit_sequence' => $this->queryValue('EditSequence', $xmlCustomer),
                'quickbooks_name_token' => md5($name)
            ];
        }

        return $this->appendContainer($quickbooksCustomers);
    }

    public function getResultTag()
    {
        return 'CustomerQueryRs';
    }

}
