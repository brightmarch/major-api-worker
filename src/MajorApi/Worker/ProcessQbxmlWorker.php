<?php

namespace MajorApi\Worker;

use MajorApi\Library\Registry;
use MajorApi\Quickbooks\QbxmlResponseProcessor;
use MajorApi\Worker\AbstractWorker;

use \Exception;

class ProcessQbxmlWorker extends AbstractWorker
{

    public function perform()
    {
        try {
            $applicationId = (int)$this->getArgument('applicationId');
            $qbxmlHash = $this->getArgument('qbxmlHash');

            $qbxml = $this->getQbxml($applicationId, $qbxmlHash);

            if (!empty($qbxml)) {
                $qbxmlResponseProcessor = new QbxmlResponseProcessor(Registry::getPostgres(), $applicationId);
                $qbxmlResponseProcessor->handle($qbxml, $qbxmlHash);
            }
        } catch (Exception $e) {
            Registry::getLogger()
                ->addError($e->getMessage(), $this->args);
        }

        return true;
    }

    private function getQbxml($applicationId, $qbxmlHash)
    {
        $query = "SELECT qq.qbxml FROM api_quickbooks_qbxml qq
            WHERE qq.application_id = ?
                AND qq.qbxml_hash = ? LIMIT 1";

        $qbxml = Registry::getPostgres()
            ->fetchColumn($query, [$applicationId, $qbxmlHash]);

        return $qbxml;
    }

}
