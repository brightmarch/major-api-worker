<?php

namespace MajorApi\Quickbooks\Command;

use MajorApi\Library\Registry;
use MajorApi\Quickbooks\IppClient;

use Doctrine\DBAL\Connection;

use \Twig_Environment;

use \DOMDocument,
    \DOMXpath,
    \ReflectionClass;

abstract class AbstractCommand
{

    /** @var Doctrine\DBAL\Connection */
    protected $postgres;

    /** @var Twig_Environment */
    protected $twig;

    /** @var MajorApi\Quickbooks\IppClient */
    protected $ippClient;

    /** @var integer */
    protected $applicationId = 0;

    /** @var integer */
    protected $quickbooksQueueId = 0;

    /** @var integer */
    protected $objectId = 0;

    /** @var array */
    protected $application = [];

    /** @var string */
    private $oauthConsumerKey = '';

    /** @var string */
    private $oauthConsumerSecret = '';

    /** @var array */
    private $parsers = [
        'AccountQueryParser' => 'MajorApi\Quickbooks\Parser\Ipp\AccountQueryParser',
        'CustomerAddParser' => 'MajorApi\Quickbooks\Parser\Ipp\CustomerAddParser',
        'ItemQueryParser' => 'MajorApi\Quickbooks\Parser\Ipp\ItemQueryParser'
    ];

    public function __construct(
        Connection $postgres,
        Twig_Environment $twig,
        $applicationId,
        $quickbooksQueueId,
        $objectId,
        $oauthConsumerKey,
        $oauthConsumerSecret
    )
    {
        $this->postgres = $postgres;
        $this->twig = $twig;
        $this->applicationId = (int)abs($applicationId);
        $this->quickbooksQueueId = (int)abs($quickbooksQueueId);
        $this->objectId = (int)abs($objectId);
        $this->oauthConsumerKey = $oauthConsumerKey;
        $this->oauthConsumerSecret = $oauthConsumerSecret;
    }

    public function getIppClient()
    {
        if (!$this->ippClient) {
            $this->getApplication();

            // Ensure an application exists and it is an IPP application.
            if ($this->hasApplication() && !empty($this->application['quickbooks_realm_id'])) {
                try {
                    $this->ippClient = IppClient::getClient(
                        $this->application['quickbooks_type'],
                        $this->oauthConsumerKey,
                        $this->oauthConsumerSecret
                    );

                    $this->ippClient->connect(
                        $this->application['quickbooks_oauth_token'],
                        $this->application['quickbooks_oauth_token_secret'],
                        $this->application['quickbooks_realm_id']
                    );
                } catch (InvalidArgumentException $e) { }
            }
        }

        return $this->ippClient;
    }

    public function setIppClient(IppClient $ippClient)
    {
        $this->ippClient = $ippClient;

        return $this;
    }

    public function getParser($parserClass, $responseXml)
    {
        $parser = null;

        if (array_key_exists($parserClass, $this->parsers)) {
            $dom = new DOMDocument;
            $xpath = new DOMXpath($dom);

            $class = new ReflectionClass($this->parsers[$parserClass]);
            $parser = $class->newInstance($responseXml, $dom, $xpath);

            unset($class);
        }

        return $parser;
    }

    public function hasApplication()
    {
        return (is_array($this->application) && count($this->application) > 0);
    }

    abstract public function execute();

    protected function getApplication()
    {
        if (!$this->hasApplication()) {
            $query = "SELECT a.* FROM web_application a WHERE a.id = ?";

            $this->application = $this->postgres->fetchAssoc($query, [$this->applicationId]);
        }

        return $this->application;
    }

    protected function saveRequestXml($requestXml)
    {
        $quickbooksRequest = [
            'created' => Registry::getTimeString(),
            'updated' => Registry::getTimeString(),
            'status' => Registry::STATUS_ENABLED,
            'application_id' => $this->applicationId,
            'quickbooks_queue_id' => $this->quickbooksQueueId,
            'object_id' => $this->objectId,
            'request_xml_hash' => md5($requestXml),
            'request_xml' => $requestXml
        ];

        $this->postgres->insert('api_quickbooks_request', $quickbooksRequest);

        return $this;
    }

}
