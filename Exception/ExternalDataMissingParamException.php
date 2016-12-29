<?php

namespace Keiwen\LolDataBundle\Exception;


class ExternalDataMissingParamException extends ExternalDataServiceException
{

    public function __construct($service, $param)
    {
        $message = '%s DataService: required "%s" parameter is missing';
        $message = sprintf($message, $service, $param);
        parent::__construct($message);
    }


}