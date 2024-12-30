<?php


namespace App\Http\Controllers\API\V1\Feed\Cards;


use App\Appointment;
use App\Dokument;
use App\Group;
use App\HasDocuments;
use App\Http\Controllers\API\V1\Feed\Card;
use App\Models\SupportTicket;
use App\Task;

class SupportCard extends Card
{
    private $additionalInformation;
    private $supportTicket;

    function __construct(SupportTicket $supportTicket, $additionalInformation) {
        $this->type = "supportCard";
        $this->additionalInformation = $additionalInformation;
        $this->supportTicket = $supportTicket;
    }

    function createPayload()
    {
        return [ "supportTicket" => $this->supportTicket , "additionalInformation" => $this->additionalInformation ];
    }
}
