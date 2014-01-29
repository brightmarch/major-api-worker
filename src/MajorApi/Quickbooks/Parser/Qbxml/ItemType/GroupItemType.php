<?php

namespace MajorApi\Quickbooks\Parser\Qbxml\ItemType;

use MajorApi\Quickbooks\Parser\Qbxml\ItemType\AbstractItemType;

class GroupItemType extends AbstractItemType
{

    /** @const string */
    const ITEM_TYPE = 'group';

    public function parse()
    {
        $description = $this->queryValue('ItemDesc', $this->xmlItem);

        $quickbooksItem = array_merge($this->initializeQuickbooksItem(), [
            'type' => self::ITEM_TYPE,
            'description' => $description,
            'item_description' => $description
        ]);

        return $quickbooksItem;
    }

}
