<?php

namespace MajorApi\Quickbooks\Parser\Qbxml\ItemType;

use MajorApi\Quickbooks\Parser\Mixin\ParserMixin;

use \DateTime,
    \DOMNode,
    \DOMXpath;

abstract class AbstractItemType
{

    use ParserMixin;

    /** @var DOMXpath */
    protected $xpath;

    /** @var DOMNode */
    protected $xmlItem;

    /** @const string */
    const IS_ACTIVE = 'true';

    public function __construct(DOMXpath $xpath, DOMNode $xmlItem)
    {
        $this->xpath = $xpath;
        $this->xmlItem = $xmlItem;
    }

    /**
     * Initializes a new QuickBooks Item array by parsing out
     * common values guaranteed to be in all QuickBooks Items.
     *
     * @return array
     */
    public function initializeQuickbooksItem()
    {
        $isActiveString = $this->queryValue('IsActive', $this->xmlItem);
        $isActive = (self::IS_ACTIVE == $isActiveString ? true : false );

        $quickbooksItem = [
            'type' => '',
            'name' => $this->queryValue('Name', $this->xmlItem),
            'fullname' => $this->queryValue('Fullname', $this->xmlItem),
            'is_active' => $isActive,
            'is_active_string' => ($isActive ? 't' : 'f'),
            'sublevel' => (int)$this->queryValue('Sublevel', $this->xmlItem),
            'sales_description' => $this->queryValue('SalesAndPurchase/SalesDesc', $this->xmlItem),
            'sales_price' => (float)$this->queryValue('SalesAndPurchase/SalesPrice', $this->xmlItem),
            'sales_expense' => 0.0,
            'sales_date' => null,
            'sales_date_string' => null,
            'purchase_description' => $this->queryValue('SalesAndPurchase/PurchaseDesc', $this->xmlItem),
            'purchase_price' => 0.0,
            'purchase_cost' => (float)$this->queryValue('SalesAndPurchase/PurchaseCost', $this->xmlItem),
            'purchase_date' => null,
            'purchase_date_string' => null,
            'description' => $this->queryValue('SalesOrPurchase/Desc', $this->xmlItem),
            'item_description' => '',
            'price' => (float)$this->queryValue('SalesOrPurchase/Price', $this->xmlItem),
            'price_percent' => (float)$this->queryValue('SalesOrPurchase/PricePercent', $this->xmlItem),
            'discount_rate' => 0.0,
            'discount_rate_percent' => 0.0,
            'bar_code' => $this->queryValue('BarCodeValue', $this->xmlItem),
            'manufacturer_part_number' => $this->queryValue('ManufacturerPartNumber', $this->xmlItem),
            'quantity_reorder' => 0,
            'quantity_on_hand' => 0,
            'quantity_on_order' => 0,
            'quantity_on_sales_order' => 0,
            'average_cost' => 0.0,
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
            'quickbooks_list_id' => $this->queryValue('ListID', $this->xmlItem),
            'quickbooks_edit_sequence' => $this->queryValue('EditSequence', $this->xmlItem),
        ];

        return $quickbooksItem;
    }

    abstract public function parse();

    protected function getDate($date)
    {
        $date = DateTime::createFromFormat('Y-m-d', $date);

        if (!$date) {
            $date = null;
        }

        return $date;
    }

    protected function formatDate($date)
    {
        if ($date instanceof DateTime) {
            return $date->format('Y-m-d H:i:s');
        }

        return null;
    }

}
