<?php

namespace Keiwen\LolDataBundle\Exception;


class ExternalDataInvalidContentException extends ExternalDataServiceException
{

    public function __construct($service = '', $detail = '')
    {
        $message = 'Invalid content in external data service';
        if(!empty($service)) $message .= " $service";
        if(!empty($detail)) $message .= ": $detail";
        parent::__construct($message);
    }


}