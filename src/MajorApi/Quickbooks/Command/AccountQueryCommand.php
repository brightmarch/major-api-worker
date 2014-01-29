<?php

namespace MajorApi\Quickbooks\Command;

use MajorApi\Quickbooks\Command\AbstractCommand;

class AccountQueryCommand extends AbstractCommand
{

    public function execute()
    {
        $responseXml = '';

        $ippClient = $this->getIppClient();
        if ($ippClient) {
            $ippClient->read('account');

            $responseXml = $ippClient->getLastResponse();
        }

        return $this->getParser('AccountQueryParser', $responseXml);
    }

}
