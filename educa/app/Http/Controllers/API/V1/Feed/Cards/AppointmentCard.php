<?php


namespace App\Http\Controllers\API\V1\Feed\Cards;


use App\Appointment;
use App\Http\Controllers\API\V1\Feed\Card;

class AppointmentCard extends Card
{
    private $appointment;
    private $organisators = [];
    private $appointmentType = "create";

    function __construct(Appointment $appointment, $appointmentType) {
        $this->type = "appointmentCard";
        $this->appointment = $appointment;
        $this->organisators = $appointment->getOrganisatorsAttribute();
        $this->appointmentType = $appointmentType;
    }

    function createPayload()
    {
        return [ "appointment" => $this->appointment, "organisators" => $this->organisators ,"typeAppointment" => $this->appointmentType];
    }
}
