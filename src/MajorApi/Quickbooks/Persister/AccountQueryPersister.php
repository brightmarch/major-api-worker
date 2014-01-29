<?php

namespace MajorApi\Quickbooks\Persister;

use MajorApi\Library\Registry;
use MajorApi\Quickbooks\Persister\AbstractPersister;

class AccountQueryPersister extends AbstractPersister
{

    public function getTableName()
    {
        return 'api_quickbooks_account';
    }

    public function getSelectQuery()
    {
        $query = "SELECT * FROM api_quickbooks_account qa
            WHERE qa.application_id = ?
                AND qa.quickbooks_name_token = ?";

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
            'name' => $entity['name'],
            'fullname' => $entity['fullname'],
            'is_active' => $entity['is_active_string'],
            'sublevel' => $entity['sublevel'],
            'type' => $entity['type'],
            'special_type' => $entity['special_type'],
            'account_number' => $entity['account_number'],
            'bank_number' => $entity['bank_number'],
            'description' => $entity['description'],
            'balance' => $entity['balance'],
            'total_balance' => $entity['total_balance'],
            'cash_flow_classification' => $entity['cash_flow_classification'],
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
            'balance' => $entity['balance'],
            'total_balance' => $entity['total_balance'],
            'quickbooks_list_id' => $entity['quickbooks_list_id'],
            'quickbooks_edit_sequence' => $entity['quickbooks_edit_sequence'],
            'quickbooks_name_token' => $entity['quickbooks_name_token']
        ];

        return $parameters;
    }

}
