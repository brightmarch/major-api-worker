<?php

namespace MajorApi\Quickbooks\Parser\Qbxml;

use MajorApi\Quickbooks\Parser\AbstractQbxmlParser;

class InvoiceAddParser extends AbstractQbxmlParser
{

    public function parse()
    {
        $quickbooksInvoices = [];

        $xmlInvoices = $this->xpath
            ->query('//QBXML/QBXMLMsgsRs/InvoiceAddRs/InvoiceRet');

        foreach ($xmlInvoices as $xmlInvoice) {
            $quickbooksInvoices[] = [
                'ref_number' => $this->queryValue('RefNumber', $xmlInvoice),
                'quickbooks_txn_id' => $this->queryValue('TxnID', $xmlInvoice),
                'quickbooks_txn_number' => $this->queryValue('TxnNumber', $xmlInvoice),
                'quickbooks_edit_sequence' => $this->queryValue('EditSequence', $xmlInvoice)
            ];
        }

        return $this->appendContainer($quickbooksInvoices);
    }

    public function getResultTag()
    {
        return 'InvoiceAddRs';
    }

}
