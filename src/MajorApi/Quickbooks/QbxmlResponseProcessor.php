<?php

namespace MajorApi\Quickbooks;

use MajorApi\Quickbooks\AbstractProcessor;

use \DOMDocument,
    \DOMXpath,
    \ReflectionClass;

class QbxmlResponseProcessor extends AbstractProcessor
{

    /** @var array */
    private $quickbooksQueues;

    /** @var array */
    private $parsers = [
        'AccountQueryPersister' => 'MajorApi\Quickbooks\Parser\Qbxml\AccountQueryParser',
        'CustomerAddPersister' => 'MajorApi\Quickbooks\Parser\Qbxml\CustomerAddParser',
        'CustomerQueryPersister' => 'MajorApi\Quickbooks\Parser\Qbxml\CustomerQueryParser',
        'HostQueryPersister' => 'MajorApi\Quickbooks\Parser\Qbxml\HostQueryParser',
        'InvoiceAddPersister' => 'MajorApi\Quickbooks\Parser\Qbxml\InvoiceAddParser',
        'ItemNonInventoryAddPersister' => 'MajorApi\Quickbooks\Parser\Qbxml\ItemNonInventoryAddParser',
        'ItemQueryPersister' => 'MajorApi\Quickbooks\Parser\Qbxml\ItemQueryParser',
        'SalesOrderAddPersister' => 'MajorApi\Quickbooks\Parser\Qbxml\SalesOrderAddParser',
        'SalesRepQueryPersister' => 'MajorApi\Quickbooks\Parser\Qbxml\SalesRepQueryParser',
        'VendorQueryPersister' => 'MajorApi\Quickbooks\Parser\Qbxml\VendorQueryParser'
    ];

    /**
     * Handles the XML from Quickbooks. Passes it on to all queue elements to be persisted.
     *
     * @param string $responseXml
     * @return boolean
     */
    public function handle($responseXml, $responseXmlHash)
    {
        $this->setResponseXml($responseXml)
            ->setResponseXmlHash($responseXmlHash)
            ->getUnprocessedQuickbooksQueues();

        if ($this->hasUnprocessedQuickbooksQueues()) {
            $this->startTimer();

            foreach ($this->quickbooksQueues as $quickbooksQueue) {
                // Get the parser based on the persister name.
                $parser = $this->getParser($quickbooksQueue['persister']);

                if ($parser) {
                    $persister = $this->getPersister($quickbooksQueue['persister'], $parser);

                    if ($persister) {
                        $persister->persist();

                        $this->incrementRecordCount($persister->getParser()->getContainer()->count());
                    }
                }

                $this->markQuickbooksQueueProcessed($quickbooksQueue['id']);
            }

            $this->stopTimer();
            $this->saveQuickbooksResponse();
        }

        return true;
    }

    public function getUnprocessedQuickbooksQueues()
    {
        if (!$this->hasUnprocessedQuickbooksQueues()) {
            $query = "SELECT qq.* FROM api_quickbooks_queue qq
                WHERE qq.application_id = ?
                    AND processed IS NULL";

            $this->quickbooksQueues = $this->postgres->fetchAll($query, [$this->applicationId]);
        }

        return $this->quickbooksQueues;
    }

    public function hasUnprocessedQuickbooksQueues()
    {
        return (is_array($this->quickbooksQueues) && count($this->quickbooksQueues) > 0);
    }

    private function getParser($persisterClass)
    {
        $parser = null;
        $hasParser = array_key_exists($persisterClass, $this->parsers);

        if ($hasParser) {
            $dom = new DOMDocument;
            $xpath = new DOMXpath($dom);

            $class = new ReflectionClass($this->parsers[$persisterClass]);
            $parser = $class->newInstance($this->responseXml, $dom, $xpath);

            unset($class);
        }

        return $parser;
    }

}
