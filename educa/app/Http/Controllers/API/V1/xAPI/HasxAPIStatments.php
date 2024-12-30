<?php

namespace App\Http\Controllers\API\V1\xAPI;

trait HasxAPIStatments
{
    abstract function getObjectType();

    function getObjectId()
    {
        return $this->id;
    }

    function getObjectDisplay()
    {
        return $this->getObjectType();
    }

    function getStatements()
    {

    }
}
