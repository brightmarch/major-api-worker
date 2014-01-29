<?php

namespace MajorApi\Worker;

use MajorApi\Library\Registry;
use MajorApi\Quickbooks\IppRequestProcessor;
use MajorApi\Worker\AbstractWorker;

use \Exception;

class ProcessIppRequestWorker extends AbstractWorker
{

    public function perform()
    {
        try {
            $postgres = Registry::getPostgres();
            $twig = Registry::getTwig();
            $majorApiConfig = Registry::getMajorApiConfig();

            $applicationId = (int)$this->getArgument('applicationId');
            $quickbooksQueueId = (int)$this->getArgument('quickbooksQueueId');
            $objectId = (int)$this->getArgument('objectId');

            $ippRequestProcessor = new IppRequestProcessor(
                $postgres,
                $twig,
                $applicationId,
                $quickbooksQueueId,
                $objectId,
                $majorApiConfig['test_ipp_oauth_consumer_key'],
                $majorApiConfig['test_ipp_oauth_consumer_secret']
            );

            $ippRequestProcessor->handle();
        } catch (Exception $e) {
            Registry::getLogger()
                ->addError($e->getMessage(), $this->args);
        }

        return true;
    }

}
