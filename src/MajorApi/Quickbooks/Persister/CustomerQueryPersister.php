<?php

namespace MajorApi\Quickbooks\Persister;

use MajorApi\Library\Registry;
use MajorApi\Quickbooks\Persister\AbstractPersister;

class CustomerQueryPersister extends AbstractPersister
{

    public function getTableName()
    {
        return 'api_quickbooks_customer';
    }

    public function getSelectQuery()
    {
        $query = "SELECT * FROM api_quickbooks_customer qc
            WHERE qc.application_id = ?
                AND qc.quickbooks_name_token = ?";

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
            'application_id' => $application['id'],
            'token' => $application['quickbooks_token'],
            'name' => $entity['name'],
            'is_active' => $entity['is_active_string'],
            'company_name' => $entity['company_name'],
            'salutation' => $entity['salutation'],
            'first_name' => $entity['first_name'],
            'middle_name' => $entity['middle_name'],
            'last_name' => $entity['last_name'],
            'job_title' => $entity['job_title'],
            'bill_address1' => $entity['bill_address1'],
            'bill_address2' => $entity['bill_address2'],
            'bill_address3' => $entity['bill_address3'],
            'bill_address4' => $entity['bill_address4'],
            'bill_address5' => $entity['bill_address5'],
            'bill_city' => $entity['bill_city'],
            'bill_state' => $entity['bill_state'],
            'bill_postal_code' => $entity['bill_postal_code'],
            'bill_country' => $entity['bill_country'],
            'bill_note' => $entity['bill_note'],
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
            'notes' => $entity['notes'],
            'quickbooks_list_id' => $entity['quickbooks_list_id'],
            'quickbooks_edit_sequence' => $entity['quickbooks_edit_sequence'],
            'quickbooks_name_token' => $entity['quickbooks_name_token']
        ];

        return $parameters;
    }

    public function getUpdateParameters(array $entity)
    {
        $application = $this->getApplication();

        $parameters = [
            'updated' => Registry::getTimeString(),
            'token' => $application['quickbooks_token'],
            'quickbooks_list_id' => $entity['quickbooks_list_id'],
            'quickbooks_edit_sequence' => $entity['quickbooks_edit_sequence']
        ];

        return $parameters;
    }

}
