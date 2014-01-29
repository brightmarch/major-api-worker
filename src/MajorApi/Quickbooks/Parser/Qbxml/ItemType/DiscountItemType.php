<?php

namespace MajorApi\Quickbooks\Parser\Qbxml\ItemType;

use MajorApi\Quickbooks\Parser\Qbxml\ItemType\AbstractItemType;

class DiscountItemType extends AbstractItemType
{

    /** @const string */
    const ITEM_TYPE = 'discount';

    public function parse()
    {
        $description = $this->queryValue('ItemDesc', $this->xmlItem);

        $quickbooksItem = array_merge($this->initializeQuickbooksItem(), [
            'type' => self::ITEM_TYPE,
            'description' => $description,
            'item_description' => $description,
            'discount_rate' => (float)$this->queryValue('DiscountRate', $this->xmlItem),
            'discount_rate_percent' => (float)$this->queryValue('DiscountRatePercent', $this->xmlItem)
        ]);

        return $quickbooksItem;
    }

}
