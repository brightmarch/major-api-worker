<?php

namespace MajorApi\Quickbooks\Parser\Qbxml\ItemType;

use MajorApi\Quickbooks\Parser\Qbxml\ItemType\AbstractItemType;

class InventoryItemType extends AbstractItemType
{

    /** @const string */
    const ITEM_TYPE = 'inventory';

    public function parse()
    {
        $quickbooksItem = array_merge($this->initializeQuickbooksItem(), [
            'type' => self::ITEM_TYPE,
            'sales_description' => $this->queryValue('SalesDesc', $this->xmlItem),
            'sales_price' => (float)$this->queryValue('SalesPrice', $this->xmlItem),
            'purchase_description' => $this->queryValue('PurchaseDesc', $this->xmlItem),
            'purchase_cost' => (float)$this->queryValue('PurchaseCost', $this->xmlItem),
            'quantity_reorder' => (int)$this->queryValue('ReorderPoint', $this->xmlItem),
            'quantity_on_hand' => (int)$this->queryValue('QuantityOnHand', $this->xmlItem),
            'quantity_on_order' => (int)$this->queryValue('QuantityOnOrder', $this->xmlItem),
            'quantity_on_sales_order' => (int)$this->queryValue('QuantityOnSalesOrder', $this->xmlItem),
            'average_cost' => (float)$this->queryValue('AverageCost', $this->xmlItem)
        ]);

        return $quickbooksItem;
    }

}
