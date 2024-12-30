<?php


namespace App\Http\Controllers\API\V1\Feed\Cards;


use App\Appointment;
use App\Dokument;
use App\Group;
use App\HasDocuments;
use App\Http\Controllers\API\V1\Feed\Card;
use App\Task;

class DocumentCard extends Card
{
    private $dokument;
    private $user;
    private $modelInformation;
    private $model;

    function __construct(Dokument $dokument) {
        $this->type = "documentCard";
        $this->dokument = $dokument;
        $this->user = $dokument->creator;
        $this->modelInformation = $dokument->modelInformation();
        $this->model = $dokument->model();
    }

    function createPayload()
    {
        return [ "document" => $this->dokument , "user" => $this->user, "model" => $this->model, "modelInformation" => $this->modelInformation ];
    }
}
