<?php

namespace MajorApi\Quickbooks\Persister;

use MajorApi\Library\Registry;
use MajorApi\Quickbooks\Persister\AbstractPersister;

class SalesOrderAddPersister extends AbstractPersister
{

    public function getTableName()
    {
        return 'api_quickbooks_order';
    }

    public function getSelectQuery()
    {
        $query = "SELECT * FROM api_quickbooks_order qo
            WHERE qo.application_id = ?
                AND qo.ref_number = ?";

        return $query;
    }

    public function getSelectParameters(array $entity)
    {
        $parameters = [
            $this->applicationId,
            $entity['ref_number']
        ];

        return $parameters;
    }

    public function getUpdateParameters(array $entity)
    {
        $application = $this->getApplication();

        $parameters = [
            'updated' => Registry::getTimeString(),
            'token' => $application['quickbooks_token'],
            'quickbooks_txn_id' => $entity['quickbooks_txn_id'],
            'quickbooks_txn_number' => $entity['quickbooks_txn_number'],
            'quickbooks_edit_sequence' => $entity['quickbooks_edit_sequence']
        ];

        return $parameters;
    }

}
