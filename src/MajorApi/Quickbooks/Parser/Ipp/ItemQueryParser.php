<?php

namespace MajorApi\Quickbooks\Parser\Ipp;

use MajorApi\Quickbooks\Parser\AbstractIppParser;

class ItemQueryParser extends AbstractIppParser
{

    public function parse()
    {
        $quickbooksItems = [];

        $xmlItems = $this->xpath->query('//ipp:RestResponse/ipp:Items/ipp:Item');

        foreach ($xmlItems as $xmlItem) {
            $name = $this->queryValue('ipp:Name', $xmlItem);
            $description = $this->queryValue('ipp:Desc', $xmlItem);

            $isActiveString = $this->queryValue('ipp:Active', $xmlItem);
            $isActive = (self::IS_ACTIVE == $isActiveString ? true : false );

            $quickbooksItems[] = [
                'type' => strtolower($this->queryValue('ipp:Type', $xmlItem)),
                'name' => $name,
                'fullname' => $name,
                'is_active' => $isActive,
                'is_active_string' => ($isActive ? 't' : 'f'),
                'sublevel' => 0,
                'sales_description' => $description,
                'sales_price' => 0.0,
                'sales_expense' => 0.0,
                'sales_date' => null,
                'sales_date_string' => null,
                'purchase_description' => $this->queryValue('ipp:PurchaseDesc', $xmlItem),
                'purchase_price' => 0.0,
                'purchase_cost' => (float)$this->queryValue('ipp:PurchaseCost/ipp:Amount', $xmlItem),
                'purchase_date' => null,
                'purchase_date_string' => null,
                'description' => $description,
                'item_description' => $description,
                'price' => (float)$this->queryValue('ipp:UnitPrice/ipp:Amount', $xmlItem),
                'price_percent' => 0.0,
                'discount_rate' => 0.0,
                'discount_rate_percent' => 0.0,
                'bar_code' => null,
                'manufacturer_part_number' => $this->queryValue('ipp:ManPartNum', $xmlItem),
                'quantity_reorder' => (int)$this->queryValue('ipp:ReorderPoint', $xmlItem),
                'quantity_on_hand' => (int)$this->queryValue('ipp:QtyOnHand', $xmlItem),
                'quantity_on_order' => (int)$this->queryValue('ipp:QtyOnPurchaseOrder', $xmlItem),
                'quantity_on_sales_order' => (int)$this->queryValue('ipp:QtyOnSalesOrder', $xmlItem),
                'average_cost' => (float)$this->queryValue('ipp:AvgCost/ipp:Amount', $xmlItem),
                'vendor_or_payee_name' => null,
                'acquired_as' => null,
                'asset_description' => null,
                'location' => null,
                'po_number' => null,
                'serial_number' => null,
                'warranty_expiration_date' => null,
                'warranty_expiration_date_string' => null,
                'notes' => null,
                'asset_number' => null,
                'cost_basis' => 0.0,
                'year_end_accumulated_depreciation' => 0.0,
                'year_end_book_value' => 0.0,
                'external_guid' => null,
                'quickbooks_list_id' => $this->queryValue('ipp:Id', $xmlItem),
                'quickbooks_edit_sequence' => $this->queryValue('ipp:SyncToken', $xmlItem)
            ];
        }

        return $this->appendContainer($quickbooksItems);
    }

}
