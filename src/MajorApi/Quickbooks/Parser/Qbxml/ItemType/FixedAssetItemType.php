<?php

namespace MajorApi\Quickbooks\Parser\Qbxml\ItemType;

use MajorApi\Quickbooks\Parser\Qbxml\ItemType\AbstractItemType;

use \DateTime;

class FixedAssetItemType extends AbstractItemType
{

    /** @const string */
    const ITEM_TYPE = 'fixed-asset';

    public function parse()
    {
        $salesDate = $this->getDate(
            $this->queryValue('FixedAssetSalesInfo/SalesDate', $this->xmlItem)
        );

        $purchaseDate = $this->getDate(
            $this->queryValue('PurchaseDate', $this->xmlItem)
        );

        $warrantyExpirationDate = $this->getDate(
            $this->queryValue('WarrantyExpDate', $this->xmlItem)
        );

        $description = $this->queryValue('PurchaseDesc', $this->xmlItem);

        $quickbooksItem = array_merge($this->initializeQuickbooksItem(), [
            'type' => self::ITEM_TYPE,
            'description' => $description,
            'acquired_as' => $this->queryValue('AcquiredAs', $this->xmlItem),
            'sales_description' => $this->queryValue('FixedAssetSalesInfo/SalesDesc', $this->xmlItem),
            'sales_date' => $salesDate,
            'sales_date_string' => $this->formatDate($salesDate),
            'sales_price' => (float)$this->queryValue('FixedAssetSalesInfo/SalesPrice', $this->xmlItem),
            'purchase_description' => $description,
            'purchase_date' => $this->getDate($this->queryValue('PurchaseDate', $this->xmlItem)),
            'purchase_date_string' => $this->formatDate($purchaseDate),
            'purchase_cost' => (float)$this->queryValue('PurchaseCost', $this->xmlItem),
            'vendor_or_payee_name' => $this->queryValue('VendorOrPayeeName', $this->xmlItem),
            'asset_description' => $this->queryValue('AssetDescription', $this->xmlItem),
            'po_number' => $this->queryValue('PONumber', $this->xmlItem),
            'serial_number' => $this->queryValue('SerialNumber', $this->xmlItem),
            'warranty_expiration_date' => $this->getDate($this->queryValue('WarrantyExpDate', $this->xmlItem)),
            'warranty_expiration_date_string' => $this->formatDate($warrantyExpirationDate),
            'notes' => $this->queryValue('Notes', $this->xmlItem),
            'cost_basis' => (float)$this->queryValue('CostBasis', $this->xmlItem),
            'year_end_accumulated_depreciation' => (float)$this->queryValue('YearEndAccumulatedDepreciation', $this->xmlItem),
            'year_end_book_value' => (float)$this->queryValue('YearEndBookValue', $this->xmlItem)
        ]);

        return $quickbooksItem;
    }

}
