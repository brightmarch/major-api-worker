<?php

namespace MajorApi\Quickbooks;

class IppDesktopClient extends IppClient
{

    public function getUrlTemplate()
    {
        return 'https://services.intuit.com/sb/%s/v2/%d';
    }

}
