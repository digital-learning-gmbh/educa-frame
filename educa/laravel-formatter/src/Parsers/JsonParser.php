<?php namespace SoapBox\Formatter\Parsers;

class JsonParser extends Parser
{

    private $json;

    public function __construct($data)
    {
        $this->json = json_decode(trim($data), true);
    }

    public function toArray()
    {
        return $this->json;
    }

}
