<?php

namespace MajorApi\Quickbooks\Persister;

use MajorApi\Library\Registry;
use MajorApi\Quickbooks\Persister\AbstractPersister;

class SalesRepQueryPersister extends AbstractPersister
{

    public function getTableName()
    {
        return 'api_quickbooks_sales_rep';
    }

    public function getSelectQuery()
    {
        $query = "SELECT qsr.* FROM api_quickbooks_sales_rep qsr
            WHERE qsr.application_id = ?
                AND qsr.initial = ?";

        return $query;
    }

    public function getSelectParameters(array $entity)
    {
        $parameters = [
            $this->applicationId,
            $entity['initial']
        ];

        return $parameters;
    }

    public function getInsertParameters(array $entity)
    {
        $parameters = [
            'created' => Registry::getTimeString(),
            'updated' => Registry::getTimeString(),
            'status' => Registry::STATUS_ENABLED,
            'application_id' => $this->applicationId,
            'initial' => $entity['initial'],
            'is_active' => $entity['is_active_string'],
            'quickbooks_list_id' => $entity['quickbooks_list_id'],
            'quickbooks_edit_sequence' => $entity['quickbooks_edit_sequence']
        ];

        return $parameters;
    }

    public function getUpdateParameters(array $entity)
    {
        $parameters = [
            'updated' => Registry::getTimeString(),
            'initial' => $entity['initial'],
            'is_active' => $entity['is_active_string']
        ];

        return $parameters;
    }

}
