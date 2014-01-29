<?php

namespace MajorApi\Quickbooks\Persister;

use MajorApi\Library\Registry;
use MajorApi\Quickbooks\Persister\AbstractPersister;

class CustomerAddPersister extends AbstractPersister
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
