<?php

namespace MajorApi\Quickbooks\Parser\Qbxml\ItemType;

use MajorApi\Quickbooks\Parser\Qbxml\ItemType\AbstractItemType;

class NonInventoryItemType extends AbstractItemType
{

    /** @const string */
    const ITEM_TYPE = 'non-inventory';

    public function parse()
    {
        $quickbooksItem = array_merge($this->initializeQuickbooksItem(), [
            'type' => self::ITEM_TYPE
        ]);

        return $quickbooksItem;
    }

}
