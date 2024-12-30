<?php


namespace App\Http\Controllers\API\V1\Feed\Cards;


use App\Appointment;
use App\Http\Controllers\API\V1\Feed\Card;
use App\Submission;
use App\Task;

class TaskRatedCard extends Card
{
    private $task;
    private $submission;
    private $user;

    function __construct(Task $task, Submission $submission) {
        $this->type = "taskRatedCard";
        $this->task = $task;
        $this->submission = $submission;
        $this->user = $task->creator;
    }

    function createPayload()
    {
        return [ "task" => $this->task, "submission" => $this->submission, "user" => $this->user ];
    }
}
