<?php

namespace MajorApi\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\AbstractQbxmlParser;

class SalesOrderAddParser extends AbstractQbxmlParser
{

    public function parse()
    {
        $quickbooksSalesOrders = [];

        $xmlSalesOrders = $this->xpath
            ->query('//QBXML/QBXMLMsgsRs/SalesOrderAddRs/SalesOrderRet');

        foreach ($xmlSalesOrders as $xmlSalesOrder) {
            $quickbooksSalesOrders[] = [
                'ref_number' => $this->queryValue('RefNumber', $xmlSalesOrder),
                'quickbooks_txn_id' => $this->queryValue('TxnID', $xmlSalesOrder),
                'quickbooks_txn_number' => $this->queryValue('TxnNumber', $xmlSalesOrder),
                'quickbooks_edit_sequence' => $this->queryValue('EditSequence', $xmlSalesOrder)
            ];
        }

        return $this->appendContainer($quickbooksSalesOrders);
    }

    public function getResultTag()
    {
        return 'SalesOrderAddRs';
    }

}
