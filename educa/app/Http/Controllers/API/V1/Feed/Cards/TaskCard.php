<?php


namespace App\Http\Controllers\API\V1\Feed\Cards;


use App\Appointment;
use App\Http\Controllers\API\V1\Feed\Card;
use App\Task;

class TaskCard extends Card
{
    private $task;
    private $creator;
    private $attendees;
    private $sections;

    function __construct(Task $task) {
        $this->type = "taskCard";
        $this->task = $task;
        $this->creator = $task->creator;
        $this->attendees = $task->attendees;
        $task->load('sections');
    }

    function createPayload()
    {
        // Status gibt den Bearbeitungsstatus des aktuellen Nutzers an
        return [ "task" => $this->task, "status" => "", "creator" =>  $this->creator, "attendees" => $this->attendees ];
    }
}
