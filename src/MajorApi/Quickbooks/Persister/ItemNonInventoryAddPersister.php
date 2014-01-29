<?php

namespace MajorApi\Quickbooks\Persister;

use MajorApi\Library\Registry;
use MajorApi\Quickbooks\Persister\AbstractPersister;

class ItemNonInventoryAddPersister extends AbstractPersister
{

    public function getTableName()
    {
        return 'api_quickbooks_item';
    }

    public function getSelectQuery()
    {
        $query = "SELECT * FROM api_quickbooks_item qi
            WHERE qi.application_id = ?
                AND qi.name = ?";

        return $query;
    }

    public function getSelectParameters(array $entity)
    {
        $parameters = [
            $this->applicationId,
            $entity['name']
        ];

        return $parameters;
    }

    public function getUpdateParameters(array $entity)
    {
        $application = $this->getApplication();

        $parameters = [
            'updated' => Registry::getTimeString(),
            'quickbooks_list_id' => $entity['quickbooks_list_id'],
            'quickbooks_edit_sequence' => $entity['quickbooks_edit_sequence']
        ];

        return $parameters;
    }

}
