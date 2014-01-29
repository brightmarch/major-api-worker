<?php

namespace MajorApi\Quickbooks;

use MajorApi\Quickbooks\AbstractProcessor;
use MajorApi\Library\Registry;

use Doctrine\DBAL\Connection;

use \Twig_Environment;

use \ReflectionClass;

class IppRequestProcessor extends AbstractProcessor
{

    /** @var Twig_Environment */
    private $twig;

    /** @var array */
    private $quickbooksQueue;

    /** @var integer */
    private $quickbooksQueueId = 0;

    /** @var integer */
    private $objectId = 0;

    /** @var string */
    private $oauthConsumerKey = '';

    /** @var string */
    private $oauthConsumerSecret = '';

    /** @var array */
    private $commands = [
        'CustomerAddCommand' => 'MajorApi\Quickbooks\Command\CustomerAddCommand',
        'ItemQueryCommand' => 'MajorApi\Quickbooks\Command\ItemQueryCommand'
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
        parent::__construct($postgres, $applicationId);
        $this->twig = $twig;
        $this->quickbooksQueueId = (int)abs($quickbooksQueueId);
        $this->objectId = (int)abs($objectId);
        $this->oauthConsumerKey = $oauthConsumerKey;
        $this->oauthConsumerSecret = $oauthConsumerSecret;
    }

    public function handle()
    {
        $this->getQuickbooksQueue();

        if ($this->hasQuickbooksQueue()) {
            $command = $this->getCommand($this->quickbooksQueue['command']);

            if ($command) {
                $parser = $command->execute();

                // Start the timer, parse, and persist the data coming back from IPP.
                $this->startTimer();
                if ($parser) {
                    $this->setResponseXml($parser->getXml())
                        ->setResponseXmlHash(md5($parser->getXml()));

                    $persister = $this->getPersister($this->quickbooksQueue['persister'], $parser);

                    if ($persister) {
                        $persister->persist();

                        $this->incrementRecordCount($persister->getParser()->getContainer()->count());
                    }
                }
                $this->stopTimer();
            }

            $this->markQuickbooksQueueProcessed($this->quickbooksQueueId);
            $this->saveQuickbooksResponse()
                ->saveResponseXml();
        }

        return true;
    }

    public function getQuickbooksQueue()
    {
        $query = "SELECT qq.* FROM api_quickbooks_queue qq
            WHERE qq.id = ?
                AND qq.application_id = ?
                AND qq.processed IS NULL";

        $parameters = [
            $this->quickbooksQueueId,
            $this->applicationId
        ];

        $this->quickbooksQueue = $this->postgres->fetchAssoc($query, $parameters);

        return $this->quickbooksQueue;
    }

    public function hasQuickbooksQueue()
    {
        return (is_array($this->quickbooksQueue) && count($this->quickbooksQueue) > 0);
    }

    private function getCommand($commandClass)
    {
        $command = null;
        $hasCommand = array_key_exists($commandClass, $this->commands);

        if ($hasCommand) {
            $class = new ReflectionClass($this->commands[$commandClass]);
            $command = $class->newInstance(
                $this->postgres,
                $this->twig,
                $this->applicationId,
                $this->quickbooksQueueId,
                $this->objectId,
                $this->oauthConsumerKey,
                $this->oauthConsumerSecret
            );

            unset($class);
        }

        return $command;
    }

    private function saveResponseXml()
    {
        $quickbooksQbxml = [
            'created' => Registry::getTimeString(),
            'updated' => Registry::getTimeString(),
            'status' => Registry::STATUS_ENABLED,
            'application_id' => $this->applicationId,
            'qbxml_hash' => $this->responseXmlHash,
            'qbxml' => $this->responseXml
        ];

        $this->postgres->insert('api_quickbooks_qbxml', $quickbooksQbxml);

        return $this;
    }

}
