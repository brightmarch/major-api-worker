<?php

namespace MajorApi\Quickbooks\Parser\Qbxml\ItemType;

use MajorApi\Quickbooks\Parser\Qbxml\ItemType\AbstractItemType;

class ServiceItemType extends AbstractItemType
{

    /** @const string */
    const ITEM_TYPE = 'service';

    public function parse()
    {
        $quickbooksItem = array_merge($this->initializeQuickbooksItem(), [
            'type' => self::ITEM_TYPE
        ]);

        return $quickbooksItem;
    }

}
