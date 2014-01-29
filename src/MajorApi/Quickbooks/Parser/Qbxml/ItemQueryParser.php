<?php

namespace MajorApi\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\AbstractQbxmlParser;

use \ReflectionClass;

class ItemQueryParser extends AbstractQbxmlParser
{

    /** @var array */
    private $parsers = [
        'ItemDiscountRet' => 'MajorApi\Quickbooks\Parser\Qbxml\ItemType\DiscountItemType',
        'ItemFixedAssetRet' => 'MajorApi\Quickbooks\Parser\Qbxml\ItemType\FixedAssetItemType',
        'ItemGroupRet' => 'MajorApi\Quickbooks\Parser\Qbxml\ItemType\GroupItemType',
        'ItemInventoryRet' => 'MajorApi\Quickbooks\Parser\Qbxml\ItemType\InventoryItemType',
        'ItemNonInventoryRet' => 'MajorApi\Quickbooks\Parser\Qbxml\ItemType\NonInventoryItemType',
        'ItemServiceRet' => 'MajorApi\Quickbooks\Parser\Qbxml\ItemType\ServiceItemType'
    ];

    public function parse()
    {
        $quickbooksItems = [];

        $xmlItems = $this->xpath
            ->query('//QBXML/QBXMLMsgsRs/ItemQueryRs/*');

        // QuickBooks returns multiple item types in an item query and they must be
        // parsed differently because they each have different nodes.
        foreach ($xmlItems as $xmlItem) {
            if (array_key_exists($xmlItem->nodeName, $this->parsers)) {
                $itemType = new ReflectionClass($this->parsers[$xmlItem->nodeName]);
                $quickbooksItem = $itemType->newInstance($this->xpath, $xmlItem)
                    ->parse();

                $quickbooksItem = $this->normalizePrice($quickbooksItem);
                $quickbooksItem = $this->normalizeDescription($quickbooksItem);

                $quickbooksItems[] = $quickbooksItem;
            }
        }

        return $this->appendContainer($quickbooksItems);
    }

    public function getResultTag()
    {
        return 'ItemQueryRs';
    }

    private function normalizePrice($quickbooksItem)
    {
        if (0 == $quickbooksItem['price']) {
            $quickbooksItem['price'] = $quickbooksItem['sales_price'];
        }

        return $quickbooksItem;
    }

    private function normalizeDescription($quickbooksItem)
    {
        if (empty($quickbooksItem['description'])) {
            $quickbooksItem['description'] = $quickbooksItem['sales_description'];
        }

        return $quickbooksItem;
    }

}
