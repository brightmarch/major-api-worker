<?php

namespace MajorApi\Quickbooks\Parser\Ipp;

use MajorApi\Quickbooks\Parser\AbstractIppParser;

class CustomerQueryParser extends AbstractIppParser
{

    public function parse()
    {
        $quickbooksCustomers = [];

        $xmlCustomers = $this->xpath->query('//ipp:RestResponse/ipp:Customers/ipp:Customer');

        foreach ($xmlCustomers as $xmlCustomer) {
            $name = $this->queryValue('ipp:Name', $xmlCustomer);
            $name = strtoupper($name);

            $isActiveString = $this->queryValue('ipp:Active', $xmlCustomer);
            $isActive = (self::IS_ACTIVE == $isActiveString ? true : false );

            // The address info below is just assumed to be in billing, shipping order, so I'm just being lazy.
            // If we run into issues, the <Address> tags will have to be parsed out, and the <Tag> tag will
            // be used to determine if an address is a billing or shipping address.
            $quickbooksCustomers[] = [
                'name' => $name,
                'is_active' => $isActive,
                'is_active_string' => ($isActive ? 't' : 'f'),
                'company_name' => $this->queryValue('ipp:DBAName', $xmlCustomer),
                'salutation' => $this->queryValue('ipp:Title', $xmlCustomer),
                'first_name' => $this->queryValue('ipp:GivenName', $xmlCustomer),
                'middle_name' => null,
                'last_name' => $this->queryValue('ippFamilyName', $xmlCustomer),
                'job_title' => null,
                'bill_address1' => $this->queryValue('ipp:Address[1]/ipp:Line1', $xmlCustomer),
                'bill_address2' => $this->queryValue('ipp:Address[1]/ipp:Line2', $xmlCustomer),
                'bill_address3' => $this->queryValue('ipp:Address[1]/ipp:Line3', $xmlCustomer),
                'bill_address4' => $this->queryValue('ipp:Address[1]/ipp:Line4', $xmlCustomer),
                'bill_address5' => $this->queryValue('ipp:Address[1]/ipp:Line5', $xmlCustomer),
                'bill_city' => $this->queryValue('ipp:Address[1]/ipp:City', $xmlCustomer),
                'bill_state' => $this->queryValue('ipp:Address[1]/ipp:CountrySubDivisionCode', $xmlCustomer),
                'bill_postal_code' => $this->queryValue('ipp:Address[1]/ipp:PostalCode', $xmlCustomer),
                'bill_country' => $this->queryValue('ipp:Address[1]/ipp:Country', $xmlCustomer),
                'bill_note' => null,
                'ship_address1' => $this->queryValue('ipp:Address[2]/ipp:Line1', $xmlCustomer),
                'ship_address2' => $this->queryValue('ipp:Address[2]/ipp:Line2', $xmlCustomer),
                'ship_address3' => $this->queryValue('ipp:Address[2]/ipp:Line3', $xmlCustomer),
                'ship_address4' => $this->queryValue('ipp:Address[2]/ipp:Line4', $xmlCustomer),
                'ship_address5' => $this->queryValue('ipp:Address[2]/ipp:Line5', $xmlCustomer),
                'ship_city' => $this->queryValue('ipp:Address[2]/ipp:City', $xmlCustomer),
                'ship_state' => $this->queryValue('ipp:Address[2]/ipp:CountrySubDivisionCode', $xmlCustomer),
                'ship_postal_code' => $this->queryValue('ipp:Address[2]/ipp:PostalCode', $xmlCustomer),
                'ship_country' => $this->queryValue('ipp:Address[2]/ipp:Country', $xmlCustomer),
                'ship_note' => null,
                'phone' => $this->queryValue('ipp:Phone[1]/ipp:FreeFormNumber', $xmlCustomer),
                'alt_phone' => $this->queryValue('ipp:Phone[2]/ipp:FreeFormNumber', $xmlCustomer),
                'fax' => null,
                'email' => $this->queryValue('ipp:Email[1]/ipp:Address', $xmlCustomer),
                'email_cc' => $this->queryValue('ipp:Email[2]/ipp:Address', $xmlCustomer),
                'notes' => null,
                'quickbooks_list_id' => $this->queryValue('ipp:Id', $xmlCustomer),
                'quickbooks_edit_sequence' => $this->queryValue('ipp:SyncToken', $xmlCustomer),
                'quickbooks_name_token' => md5($name)
            ];
        }

        return $this->appendContainer($quickbooksCustomers);
    }

}
