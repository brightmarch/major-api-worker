<?php

namespace MajorApi\Quickbooks;

use MajorApi\Library\Registry;
use MajorApi\Quickbooks\Parser\AbstractParser;

use Doctrine\DBAL\Connection;

use \ReflectionClass;

abstract class AbstractProcessor
{

    /** @var Doctrine\DBAL\Connection */
    protected $postgres;

    /** @var integer */
    protected $applicationId = 0;

    /** @var string */
    protected $responseXml = '';

    /** @var string */
    protected $responseXmlHash = '';

    /** @var float */
    protected $startTime = 0.0;

    /** @var float */
    protected $processTime = 0.0;

    /** @var integer */
    protected $recordCount = 0;

    /** @var array */
    private $persisters = [
        'AccountQueryPersister' => 'MajorApi\Quickbooks\Persister\AccountQueryPersister',
        'CustomerAddPersister' => 'MajorApi\Quickbooks\Persister\CustomerAddPersister',
        'CustomerQueryPersister' => 'MajorApi\Quickbooks\Persister\CustomerQueryPersister',
        'HostQueryPersister' => 'MajorApi\Quickbooks\Persister\HostQueryPersister',
        'InvoiceAddPersister' => 'MajorApi\Quickbooks\Persister\InvoiceAddPersister',
        'ItemNonInventoryAddPersister' => 'MajorApi\Quickbooks\Persister\ItemNonInventoryAddPersister',
        'ItemQueryPersister' => 'MajorApi\Quickbooks\Persister\ItemQueryPersister',
        'SalesOrderAddPersister' => 'MajorApi\Quickbooks\Persister\SalesOrderAddPersister',
        'SalesRepQueryPersister' => 'MajorApi\Quickbooks\Persister\SalesRepQueryPersister',
        'VendorQueryPersister' => 'MajorApi\Quickbooks\Persister\VendorQueryPersister'
    ];

    public function __construct(Connection $postgres, $applicationId)
    {
        $this->postgres = $postgres;
        $this->applicationId = (int)abs($applicationId);
    }

    public function setResponseXml($responseXml)
    {
        $this->responseXml = $responseXml;

        return $this;
    }

    public function setResponseXmlHash($responseXmlHash)
    {
        $this->responseXmlHash = $responseXmlHash;

        return $this;
    }

    /**
     * Constructs a new Persister instance based on the
     * Persister class of the QuickBooks Queue record and
     * an already instantiated Parser object.
     *
     * @param string $persisterClass
     * @param MajorApi\Quickbooks\Parser\AbstractParser $parser
     * @return mixed
     */
    protected function getPersister($persisterClass, AbstractParser $parser)
    {
        $persister = null;
        $hasPersister = array_key_exists($persisterClass, $this->persisters);

        if ($hasPersister) {
            $class = new ReflectionClass($this->persisters[$persisterClass]);
            $persister = $class->newInstance($this->postgres, $parser, $this->applicationId);

            unset($class);
        }

        return $persister;
    }

    /**
     * Markes a QuickBooks Queue record with a processed time
     * so it can not be processed again.
     *
     * @param integer $quickbooksQueueId
     * @return boolean
     */
    protected function markQuickbooksQueueProcessed($quickbooksQueueId)
    {
        $parameters = [
            'processed' => Registry::getTimeString(),
            'request_xml' => $this->responseXmlHash
        ];

        $where = ['id' => (int)$quickbooksQueueId];
        $this->postgres->update('api_quickbooks_queue', $parameters, $where);

        return true;
    }

    /**
     * Saves the QBXML response information to the database. This
     * includes basic metrics like the size in bytes of the XML,
     * how many total records were processed, and how long it took
     * to process them.
     *
     * @return MajorApi\Quickbooks\ResponseManager
     */
    protected function saveQuickbooksResponse()
    {
        $parameters = [
            'created' => Registry::getTimeString(),
            'updated' => Registry::getTimeString(),
            'processed' => Registry::getTimeString(),
            'status' => Registry::STATUS_ENABLED,
            'application_id' => $this->applicationId,
            'response_xml' => $this->responseXmlHash,
            'xml_size' => strlen($this->responseXml),
            'record_count' => $this->recordCount,
            'process_time' => $this->processTime
        ];

        $this->postgres->insert('api_quickbooks_response', $parameters);

        return $this;
    }

    protected function startTimer()
    {
        $this->startTime = microtime(true);

        return true;
    }

    protected function stopTimer()
    {
        $this->processTime = microtime(true) - $this->startTime;

        return true;
    }

    protected function incrementRecordCount($increment)
    {
        $this->recordCount += (int)abs($increment);

        return $this;
    }

}
