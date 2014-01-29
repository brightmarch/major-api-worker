<?php

namespace MajorApi\Quickbooks\Parser\Ipp;

use MajorApi\Quickbooks\Parser\AbstractIppParser;

class VendorQueryParser extends AbstractIppParser
{

    public function parse()
    {
        $quickbooksVendors = [];

        $xmlVendors = $this->xpath->query('//ipp:RestResponse/ipp:Vendors/ipp:Vendor');

        foreach ($xmlVendors as $xmlVendor) {
            $name = $this->queryValue('ipp:Name', $xmlVendor);
            $name = strtoupper($name);

            $isActiveString = $this->queryValue('ipp:Active', $xmlVendor);
            $isActive = (self::IS_ACTIVE == $isActiveString ? true : false);

            $isVendorEligibleFor1099String = $this->queryValue('ipp:Vendor1099', $xmlVendor);
            $isVendorEligibleFor1099 = (self::IS_ACTIVE == $isVendorEligibleFor1099String ? true : false);

            $quickbooksVendors[] = [
                'name' => $name,
                'is_active' => $isActive,
                'is_active_string' => ($isActive ? 't' : 'f'),
                'company_name' => $this->queryValue('ipp:DBAName', $xmlVendor),
                'salutation' => $this->queryValue('ipp:Title', $xmlVendor),
                'first_name' => $this->queryValue('ipp:GivenName', $xmlVendor),
                'middle_name' => null,
                'last_name' => $this->queryValue('ipp:FamilyName', $xmlVendor),
                'job_title' => null,
                'vendor_address_address1' => $this->queryValue('ipp:Address[1]/ipp:Line1', $xmlVendor),
                'vendor_address_address2' => $this->queryValue('ipp:Address[1]/ipp:Line2', $xmlVendor),
                'vendor_address_address3' => $this->queryValue('ipp:Address[1]/ipp:Line3', $xmlVendor),
                'vendor_address_address4' => $this->queryValue('ipp:Address[1]/ipp:Line4', $xmlVendor),
                'vendor_address_address5' => $this->queryValue('ipp:Address[1]/ipp:Line5', $xmlVendor),
                'vendor_address_city' => $this->queryValue('ipp:Address[1]/ipp:City', $xmlVendor),
                'vendor_address_state' => $this->queryValue('ipp:Address[1]/ipp:CountrySubDivisionCode', $xmlVendor),
                'vendor_address_postal_code' => $this->queryValue('ipp:Address[1]/ipp:PostalCode', $xmlVendor),
                'vendor_address_country' => $this->queryValue('ipp:Address[1]/ipp:Country', $xmlVendor),
                'vendor_address_note' => null,
                'ship_address1' => $this->queryValue('ipp:Address[2]/ipp:Line1', $xmlVendor),
                'ship_address2' => $this->queryValue('ipp:Address[2]/ipp:Line2', $xmlVendor),
                'ship_address3' => $this->queryValue('ipp:Address[2]/ipp:Line3', $xmlVendor),
                'ship_address4' => $this->queryValue('ipp:Address[2]/ipp:Line4', $xmlVendor),
                'ship_address5' => $this->queryValue('ipp:Address[2]/ipp:Line5', $xmlVendor),
                'ship_city' => $this->queryValue('ipp:Address[2]/ipp:City', $xmlVendor),
                'ship_state' => $this->queryValue('ipp:Address[2]/ipp:CountrySubDivisionCode', $xmlVendor),
                'ship_postal_code' => $this->queryValue('ipp:Address[2]/ipp:PostalCode', $xmlVendor),
                'ship_country' => $this->queryValue('ipp:Address[2]/ipp:Country', $xmlVendor),
                'ship_note' => null,
                'phone' => $this->queryValue('ipp:Phone[1]/ipp:FreeFormNumber', $xmlVendor),
                'alt_phone' => $this->queryValue('ipp:Phone[2]/ipp:FreeFormNumber', $xmlVendor),
                'fax' => null,
                'email' => $this->queryValue('ipp:Email[1]/ipp:Address', $xmlVendor),
                'email_cc' => $this->queryValue('ipp:Email[2]/ipp:Address', $xmlVendor),
                'contact' => null,
                'alt_contact' => null,
                'name_on_check' => null,
                'account_number' => $this->queryValue('ipp:AcctNum', $xmlVendor),
                'notes' => null,
                'credit_limit' => 0.0,
                'vendor_tax_identity' => null,
                'is_vendor_eligible_for_1099' => $isVendorEligibleFor1099,
                'is_vendor_eligible_for_1099_string' => ($isVendorEligibleFor1099 ? 't' : 'f'),
                'balance' => (float)$this->queryValue('ipp:OpenBalance/ipp:Amount', $xmlVendor),
                'quickbooks_list_id' => $this->queryValue('ipp:Id', $xmlVendor),
                'quickbooks_edit_sequence' => $this->queryValue('ipp:SyncToken', $xmlVendor),
                'quickbooks_name_token' => md5($name),
                'vendor_contacts' => [],
                'vendor_notes' => []
            ];
        }

        return $this->appendContainer($quickbooksVendors);
    }

}
