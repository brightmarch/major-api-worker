<?php

namespace MajorApi\Quickbooks\Persister;

use MajorApi\Library\Registry;
use MajorApi\Quickbooks\Persister\AbstractPersister;

class ItemQueryPersister extends AbstractPersister
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

    public function getInsertParameters(array $entity)
    {
        $application = $this->getApplication();

        $parameters = [
            'created' => Registry::getTimeString(),
            'updated' => Registry::getTimeString(),
            'status' => Registry::STATUS_ENABLED,
            'application_id' => $application['id'],
            'type' => $entity['type'],
            'name' => $entity['name'],
            'fullname' => $entity['fullname'],
            'is_active' => $entity['is_active_string'],
            'sublevel' => $entity['sublevel'],
            'sales_description' => $entity['sales_description'],
            'sales_price' => $entity['sales_price'],
            'sales_expense' => $entity['sales_expense'],
            'sales_date' => $entity['sales_date_string'],
            'purchase_description' => $entity['purchase_description'],
            'purchase_price' => $entity['purchase_price'],
            'purchase_cost' => $entity['purchase_cost'],
            'purchase_date' => $entity['purchase_date_string'],
            'description' => $entity['description'],
            'item_description' => $entity['item_description'],
            'price' => $entity['price'],
            'price_percent' => $entity['price_percent'],
            'discount_rate' => $entity['discount_rate'],
            'discount_rate_percent' => $entity['discount_rate_percent'],
            'bar_code' => $entity['bar_code'],
            'manufacturer_part_number' => $entity['manufacturer_part_number'],
            'quantity_reorder' => $entity['quantity_reorder'],
            'quantity_on_hand' => $entity['quantity_on_hand'],
            'quantity_on_order' => $entity['quantity_on_order'],
            'quantity_on_sales_order' => $entity['quantity_on_sales_order'],
            'average_cost' => $entity['average_cost'],
            'vendor_or_payee_name' => $entity['vendor_or_payee_name'],
            'acquired_as' => $entity['acquired_as'],
            'asset_description' => $entity['asset_description'],
            'location' => $entity['location'],
            'po_number' => $entity['po_number'],
            'serial_number' => $entity['serial_number'],
            'warranty_expiration_date' => $entity['warranty_expiration_date_string'],
            'notes' => $entity['notes'],
            'asset_number' => $entity['asset_number'],
            'cost_basis' => $entity['cost_basis'],
            'year_end_accumulated_depreciation' => $entity['year_end_accumulated_depreciation'],
            'year_end_book_value' => $entity['year_end_book_value'],
            'external_guid' => $entity['external_guid'],
            'quickbooks_list_id' => $entity['quickbooks_list_id'],
            'quickbooks_edit_sequence' => $entity['quickbooks_edit_sequence']
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
