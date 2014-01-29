<?php

namespace MajorApi\Quickbooks\Command;

use MajorApi\Quickbooks\Command\AbstractCommand;

class CustomerAddCommand extends AbstractCommand
{

    public function execute()
    {
        $responseXml = '';

        $ippClient = $this->getIppClient();
        if ($ippClient) {
            $query = "SELECT qc.* FROM api_quickbooks_customer qc
                WHERE qc.id = ?
                    AND qc.application_id = ?
                    AND qc.quickbooks_list_id IS NULL
                ORDER BY qc.created ASC LIMIT 1";

            $parameters = [
                $this->objectId,
                $this->applicationId
            ];

            $quickbooksCustomer = $this->postgres->fetchAssoc($query, $parameters);

            if ($quickbooksCustomer) {
                $application = $this->getApplication();

                $parameters = [
                    'requestId' => $ippClient->getRequestId(),
                    'realmId' => $application['quickbooks_realm_id'],
                    'quickbooksCustomer' => $quickbooksCustomer
                ];

                $requestXml = $this->twig->render('ipp-customer.xml', $parameters);
                $this->saveRequestXml($requestXml);

                $ippClient->create('customer', $requestXml);
                $responseXml = $ippClient->getLastResponse();
            }
        }

        return $this->getParser('CustomerAddParser', $responseXml);
    }

}
