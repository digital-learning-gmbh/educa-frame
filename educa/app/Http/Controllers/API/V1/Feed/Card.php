<?php


namespace App\Http\Controllers\API\V1\Feed;


abstract class Card
{
    public $id;
    public $type;
    public $timestamp;

    public $payload;

    abstract function createPayload();

    function generatePayload()
    {
        $this->payload = $this->createPayload();
    }
}
