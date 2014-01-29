<?php

namespace MajorApi\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\AbstractQbxmlParser;

use \DOMNode;

class VendorQueryParser extends AbstractQbxmlParser
{

    public function parse()
    {
        $quickbooksVendors = [];

        $xmlVendors = $this->xpath
            ->query('//QBXML/QBXMLMsgsRs/VendorQueryRs/VendorRet');

        foreach ($xmlVendors as $xmlVendor) {
            $name = $this->queryValue('Name', $xmlVendor);
            $name = strtoupper($name);

            $isActiveString = $this->queryValue('IsActive', $xmlVendor);
            $isActive = (self::IS_ACTIVE == $isActiveString ? true : false);

            $isVendorEligibleFor1099String = $this->queryValue('IsVendorEligibleFor1099', $xmlVendor);
            $isVendorEligibleFor1099 = (self::IS_ACTIVE == $isVendorEligibleFor1099String ? true : false);

            $quickbooksVendors[] = [
                'name' => $name,
                'is_active' => $isActive,
                'is_active_string' => ($isActive ? 't' : 'f'),
                'company_name' => $this->queryValue('CompanyName', $xmlVendor),
                'salutation' => $this->queryValue('Salutation', $xmlVendor),
                'first_name' => $this->queryValue('FirstName', $xmlVendor),
                'middle_name' => $this->queryValue('MiddleName', $xmlVendor),
                'last_name' => $this->queryValue('LastName', $xmlVendor),
                'job_title' => $this->queryValue('JobTitle', $xmlVendor),
                'vendor_address_address1' => $this->queryValue('VendorAddress/Addr1', $xmlVendor),
                'vendor_address_address2' => $this->queryValue('VendorAddress/Addr2', $xmlVendor),
                'vendor_address_address3' => $this->queryValue('VendorAddress/Addr3', $xmlVendor),
                'vendor_address_address4' => $this->queryValue('VendorAddress/Addr4', $xmlVendor),
                'vendor_address_address5' => $this->queryValue('VendorAddress/Addr5', $xmlVendor),
                'vendor_address_city' => $this->queryValue('VendorAddress/City', $xmlVendor),
                'vendor_address_state' => $this->queryValue('VendorAddress/State', $xmlVendor),
                'vendor_address_postal_code' => $this->queryValue('VendorAddress/PostalCode', $xmlVendor),
                'vendor_address_country' => $this->queryValue('VendorAddress/Country', $xmlVendor),
                'vendor_address_note' => $this->queryValue('VendorAddress/Note', $xmlVendor),
                'ship_address1' => $this->queryValue('ShipAddress/Addr1', $xmlVendor),
                'ship_address2' => $this->queryValue('ShipAddress/Addr2', $xmlVendor),
                'ship_address3' => $this->queryValue('ShipAddress/Addr3', $xmlVendor),
                'ship_address4' => $this->queryValue('ShipAddress/Addr4', $xmlVendor),
                'ship_address5' => $this->queryValue('ShipAddress/Addr5', $xmlVendor),
                'ship_city' => $this->queryValue('ShipAddress/City', $xmlVendor),
                'ship_state' => $this->queryValue('ShipAddress/State', $xmlVendor),
                'ship_postal_code' => $this->queryValue('ShipAddress/PostalCode', $xmlVendor),
                'ship_country' => $this->queryValue('ShipAddress/Country', $xmlVendor),
                'ship_note' => $this->queryValue('ShipAddress/Note', $xmlVendor),
                'phone' => $this->queryValue('Phone', $xmlVendor),
                'alt_phone' => $this->queryValue('AltPhone', $xmlVendor),
                'fax' => $this->queryValue('Fax', $xmlVendor),
                'email' => $this->queryValue('Email', $xmlVendor),
                'email_cc' => $this->queryValue('Cc', $xmlVendor),
                'contact' => $this->queryValue('Contact', $xmlVendor),
                'alt_contact' => $this->queryValue('AltContact', $xmlVendor),
                'name_on_check' => $this->queryValue('NameOnCheck', $xmlVendor),
                'account_number' => $this->queryValue('AccountNumber', $xmlVendor),
                'notes' => $this->queryValue('Notes', $xmlVendor),
                'credit_limit' => (float)$this->queryValue('CreditLimit', $xmlVendor),
                'vendor_tax_identity' => $this->queryValue('VendorTaxIdent', $xmlVendor),
                'is_vendor_eligible_for_1099' => $isVendorEligibleFor1099,
                'is_vendor_eligible_for_1099_string' => ($isVendorEligibleFor1099 ? 't' : 'f'),
                'balance' => (float)$this->queryValue('Balance', $xmlVendor),
                'quickbooks_list_id' => $this->queryValue('ListID', $xmlVendor),
                'quickbooks_edit_sequence' => $this->queryValue('EditSequence', $xmlVendor),
                'quickbooks_name_token' => md5($name),
                'vendor_contacts' => $this->getVendorContacts($xmlVendor),
                'vendor_notes' => $this->getVendorNotes($xmlVendor)
            ];
        }

        return $this->appendContainer($quickbooksVendors);
    }

    public function getResultTag()
    {
        return 'VendorQueryRs';
    }

    private function getVendorContacts(DOMNode $xmlVendor)
    {
        $vendorContacts = [];

        $xmlVendorContacts = $this->xpath->query('ContactsRet', $xmlVendor);
        foreach ($xmlVendorContacts as $xmlVendorContact) {
            $vendorContacts[] = [
                'created' => $this->getDate('TimeCreated', $xmlVendorContact),
                'updated' => $this->getDate('TimeModified', $xmlVendorContact),
                'contact' => $this->queryValue('Contact', $xmlVendorContact),
                'salutation' => $this->queryValue('Salutation', $xmlVendorContact),
                'first_name' => $this->queryValue('FirstName', $xmlVendorContact),
                'middle_name' => $this->queryValue('MiddleName', $xmlVendorContact),
                'last_name' => $this->queryValue('LastName', $xmlVendorContact),
                'job_title' => $this->queryValue('JobTitle', $xmlVendorContact),
                'quickbooks_list_id' => $this->queryValue('ListID', $xmlVendorContact),
                'quickbooks_edit_sequence' => $this->queryValue('EditSequence', $xmlVendorContact)
            ];
        }

        return $vendorContacts;
    }

    private function getVendorNotes(DOMNode $xmlVendor)
    {
        $vendorNotes = [];

        $xmlVendorNotes = $this->xpath->query('AdditionalNotesRet', $xmlVendor);
        foreach ($xmlVendorNotes as $xmlVendorNote) {
            $vendorNotes[] = [
                'note_date' => $this->getDate('Date', $xmlVendorNote),
                'note' => $this->queryValue('Note', $xmlVendorNote),
                'quickbooks_list_id' => $this->queryValue('NoteID', $xmlVendorNote)
            ];
        }

        return $vendorNotes;
    }

}
