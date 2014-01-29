<?php

namespace MajorApi\Quickbooks\Command;

use MajorApi\Quickbooks\Command\AbstractCommand;

class ItemQueryCommand extends AbstractCommand
{

    public function execute()
    {
        $responseXml = '';

        $ippClient = $this->getIppClient();
        if ($ippClient) {
            $ippClient->read('item');

            $responseXml = $ippClient->getLastResponse();
        }

        return $this->getParser('ItemQueryParser', $responseXml);
    }

}
