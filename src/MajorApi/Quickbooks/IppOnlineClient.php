<?php

namespace MajorApi\Quickbooks;

class IppOnlineClient extends IppClient
{

    public function getUrlTemplate()
    {
        return 'https://qbo.sbfinance.intuit.com/resource/%s/v2/%d';
    }

}
