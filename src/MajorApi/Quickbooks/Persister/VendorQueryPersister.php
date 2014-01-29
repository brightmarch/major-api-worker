<?php

namespace MajorApi\Quickbooks\Persister;

use MajorApi\Library\Registry;
use MajorApi\Quickbooks\Persister\AbstractPersister;

class VendorQueryPersister extends AbstractPersister
{

    public function getTableName()
    {
        return 'api_quickbooks_vendor';
    }

    public function getSelectQuery()
    {
        $query = "SELECT qv.* FROM api_quickbooks_vendor qv
            WHERE qv.application_id = ?
                AND qv.quickbooks_name_token = ?";

        return $query;
    }

    public function getSelectParameters(array $entity)
    {
        $parameters = [
            $this->applicationId,
            $entity['quickbooks_name_token']
        ];

        return $parameters;
    }

    public function getInsertParameters(array $entity)
    {
        $application = $this->getApplication();

        $parameters = [
            'created' => Registry::getTimeString(),
            'updated' => Registry::getTimeString(),
            'status' => Registry::STATUS_ENABLED,
            'application_id' => $this->applicationId,
            'token' => $application['quickbooks_token'],
            'name' => $entity['name'],
            'is_active' => $entity['is_active_string'],
            'company_name' => $entity['company_name'],
            'salutation' => $entity['salutation'],
            'first_name' => $entity['first_name'],
            'middle_name' => $entity['middle_name'],
            'last_name' => $entity['last_name'],
            'job_title' => $entity['job_title'],
            'vendor_address_address1' => $entity['vendor_address_address1'],
            'vendor_address_address2' => $entity['vendor_address_address2'],
            'vendor_address_address3' => $entity['vendor_address_address3'],
            'vendor_address_address4' => $entity['vendor_address_address4'],
            'vendor_address_address5' => $entity['vendor_address_address5'],
            'vendor_address_city' => $entity['vendor_address_city'],
            'vendor_address_state' => $entity['vendor_address_state'],
            'vendor_address_postal_code' => $entity['vendor_address_postal_code'],
            'vendor_address_country' => $entity['vendor_address_country'],
            'vendor_address_note' => $entity['vendor_address_note'],
            'ship_address1' => $entity['ship_address1'],
            'ship_address2' => $entity['ship_address2'],
            'ship_address3' => $entity['ship_address3'],
            'ship_address4' => $entity['ship_address4'],
            'ship_address5' => $entity['ship_address5'],
            'ship_city' => $entity['ship_city'],
            'ship_state' => $entity['ship_state'],
            'ship_postal_code' => $entity['ship_postal_code'],
            'ship_country' => $entity['ship_country'],
            'ship_note' => $entity['ship_note'],
            'phone' => $entity['phone'],
            'alt_phone' => $entity['alt_phone'],
            'fax' => $entity['fax'],
            'email' => $entity['email'],
            'email_cc' => $entity['email_cc'],
            'contact' => $entity['contact'],
            'alt_contact' => $entity['alt_contact'],
            'name_on_check' => $entity['name_on_check'],
            'account_number' => $entity['account_number'],
            'notes' => $entity['notes'],
            'credit_limit' => $entity['credit_limit'],
            'vendor_tax_identity' => $entity['vendor_tax_identity'],
            'is_vendor_eligible_for_1099' => $entity['is_vendor_eligible_for_1099_string'],
            'balance' => $entity['balance'],
            'quickbooks_list_id' => $entity['quickbooks_list_id'],
            'quickbooks_edit_sequence' => $entity['quickbooks_edit_sequence'],
            'quickbooks_name_token' => $entity['quickbooks_name_token']
        ];

        return $parameters;
    }

    public function getUpdateParameters(array $entity)
    {
        $parameters = [
            'updated' => Registry::getTimeString()
        ];

        return $parameters;
    }

    protected function postInsertHook(array $entity)
    {
        // Get the ID so we can refer to the correct vendor.
        $postgres = $this->postgres;
        $id = (int)$postgres->lastInsertId('api_quickbooks_vendor_id_seq');

        // This method is run in the same transaction that the original vendor was created with.
        if ($id > 0) {
            // Insert the vendor contacts.
            foreach ($entity['vendor_contacts'] as $vendorContact) {
                $parameters = [
                    'created' => Registry::getTimeString(),
                    'updated' => Registry::getTimeString(),
                    'status' => Registry::STATUS_ENABLED,
                    'quickbooks_vendor_id' => $id,
                    'salutation' => $vendorContact['salutation'],
                    'first_name' => $vendorContact['first_name'],
                    'middle_name' => $vendorContact['middle_name'],
                    'last_name' => $vendorContact['last_name'],
                    'job_title' => $vendorContact['job_title'],
                    'quickbooks_list_id' => $vendorContact['quickbooks_list_id'],
                    'quickbooks_edit_sequence' => $vendorContact['quickbooks_edit_sequence']
                ];

                $postgres->insert('api_quickbooks_vendor_contact', $parameters);
            }

            // Insert the vendor notes.
            foreach ($entity['vendor_notes'] as $vendorNote) {
                $parameters = [
                    'created' => Registry::getTimeString(),
                    'updated' => Registry::getTimeString(),
                    'status' => Registry::STATUS_ENABLED,
                    'quickbooks_vendor_id' => $id,
                    'note_date' => $vendorNote['note_date']->format('Y-m-d H:i:s'),
                    'note' => $vendorNote['note'],
                    'quickbooks_list_id' => $vendorNote['quickbooks_list_id']
                ];

                $postgres->insert('api_quickbooks_vendor_note', $parameters);
            }
        }

        return true;
    }

}
