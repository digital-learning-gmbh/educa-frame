<?php


namespace App\Http\Controllers\API\V1\Feed\Cards;


use App\Appointment;
use App\Group;
use App\Http\Controllers\API\V1\Feed\Card;
use App\Task;

class GroupCard extends Card
{
    private $group;

    function __construct(Group $group, $user) {
        $this->type = "groupCard";
        $this->group = $group;
        $this->user = $user;
    }

    function createPayload()
    {
        return [ "group" => $this->group , "user" => $this->user ];
    }
}
