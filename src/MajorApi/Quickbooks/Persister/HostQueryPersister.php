<?php

namespace MajorApi\Quickbooks\Persister;

use MajorApi\Library\Registry;
use MajorApi\Quickbooks\Persister\AbstractPersister;

class HostQueryPersister extends AbstractPersister
{

    public function getTableName()
    {
        return 'web_application';
    }

    public function getSelectQuery()
    {
        $query = "SELECT a.* FROM web_application a
            WHERE a.id = ?";

        return $query;
    }

    public function getSelectParameters(array $entity)
    {
        $parameters = [
            $this->applicationId
        ];

        return $parameters;
    }

    public function getUpdateParameters(array $entity)
    {
        $parameters = [
            'updated' => Registry::getTimeString(),
            'quickbooks_product_name' => $entity['quickbooks_product_name'],
            'quickbooks_major_version' => $entity['quickbooks_major_version'],
            'quickbooks_minor_version' => $entity['quickbooks_minor_version'],
            'quickbooks_country' => $entity['quickbooks_country'],
            'quickbooks_supported_qbxml_version' => $entity['quickbooks_supported_qbxml_version'],
            'quickbooks_is_automatic_login' => $entity['quickbooks_is_automatic_login_string'],
            'quickbooks_qb_file_mode' => $entity['quickbooks_qb_file_mode']
        ];

        return $parameters;
    }

}
