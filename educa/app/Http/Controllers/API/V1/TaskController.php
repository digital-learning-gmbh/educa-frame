<?php

namespace App\Http\Controllers\API\V1;

use App\CloudID;
use App\Dokument;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\V1\AI\LearnAIController;
use App\Http\Controllers\API\V1\H5P\Services\DiskContentStorage;
use App\Http\Controllers\API\V1\H5P\Services\DiskLibraryStorage;
use App\Http\Controllers\API\V1\xAPI\XAPIBaseController;
use App\Models\InteractiveUserData;
use App\Models\LearnContent;
use App\Models\TaskTemplateSubmissionTemplate;
use App\Observers\FeedObserver;
use App\PermissionConstants;
use App\Section;
use App\Submission;
use App\SubmissionTemplate;
use App\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use StuPla\CloudSDK\formular\models\Formular;
use StuPla\CloudSDK\formular\models\FormularRevision;
use ZipArchive;

class TaskController extends ApiController
{
    public static $SUPPORTED_HANDIN_TYPES = ["no", "text", "file"];

    /**
     * @OA\Post (
     *     tags={"v1","task"},
     *     path="/api/v1/task",
     *     description="Tasks of the current user",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="type",
     *       required=false,
     *       in="query",
     *       description="type: open, review, close, if the parameter is not set, it will return any type",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="start",
     *       required=false,
     *       in="query",
     *       description="start timestamp",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="end",
     *       required=false,
     *       in="query",
     *       description="end timestamp",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groups",
     *       required=false,
     *       in="query",
     *       description="ids of groups which tasks should be returned, if this parameter is not used, all tasks are returned of the groups of the current user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="direct",
     *       required=false,
     *       in="query",
     *       description="default: false, direct innvations to tasks",
     *         @OA\Schema(
     *           type="boolean"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function getTask(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        // override
        if($request->has("viewForCloudId") && CloudID::find($request->viewForCloudId))
        {
            $cloud_user = CloudID::find($request->viewForCloudId);
        }

        if($request->has("sections"))
        {
            $taskController = new \App\Http\Controllers\API\V1\Groups\TaskController();
            return $taskController->getTask($request->input("sections")[0], $request);
        }

        if (!$request->has("groups")) {
            $groups = $cloud_user->gruppen()->pluck("id"); // all group Ids
        } else {
            $groups = $request->input("groups", "");
        }
        $direct = $request->input("direct", false);
        $type = $request->input("type");

        $sectionIds = Section::whereIn('group_id', $groups)->pluck('id');
        $tasks = self::loadTaskForType($cloud_user, $sectionIds, $direct, $type, null,false, $request->input("myTask",false));

        foreach ($tasks as $task)
        {
           foreach($task->submissions as $submission)
               if($submission->cloudid == $cloud_user->id)
                   $task->is_submission_seen = $submission->has_seen;
            $task->unsetRelation("submissions");
        }

        // Statistics

        $durations = 10;
        $date = Carbon::yesterday();

        $labels = [];
        $datasets = [];

        $taskDueDayDataSet = [];
        $taskDueDayDataSet["label"] = "Anzahl der Aufgaben fällig an diesem Tag";
        $taskDueDayDataSet["data"] = [];
        $taskDueDayDataSet["fill"] = true;
        $taskDueDayDataSet["backgroundColor"] = "rgb(255, 99, 132)";
        $taskDueDayDataSet["borderColor"] = "rgba(255, 99, 132, 0.2)";

        for ($i = 0; $i < $durations; $i++) {
            $labels[] = $date->format("d.m.Y");
            $count = 0;
            foreach ($tasks as $task) {
                if (Carbon::parse($task->end)->isSameDay($date)) {
                    $count++;
                }
                if($task->cloud_id != $cloud_user->id)
                {
                    unset($task->privatenote);
                }
            }
            $taskDueDayDataSet["data"][] = $count;
            $date = $date->addDay();
        }
        $datasets[] = $taskDueDayDataSet;

        return $this->createJsonResponse("ok", false, 200, ["tasks" => $tasks, "statistics" => ["labels" => $labels, "datasets" => $datasets]]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","task"},
     *     path="/api/v1/tasks/archived",
     *     description="archived tasks of the current user",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groups",
     *       required=false,
     *       in="query",
     *       description="ids of groups which tasks should be returned, if this parameter is not used, all tasks are returned of the groups of the current user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function getArchivedTasks(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        if (!$request->has("groups")) {
            $groups = $cloud_user->gruppen()->pluck("id"); // all group Ids
        } else {
            $groups = $request->input("groups", "");
        }

        // override
        if($request->has("viewForCloudId") && CloudID::find($request->viewForCloudId))
        {
            $cloud_user = CloudID::find($request->viewForCloudId);
            $groups = $cloud_user->gruppen()->pluck("id");
        }


        $sectionIds = Section::whereIn('group_id', $groups)->pluck('id');
        $tasks = self::loadTaskForType($cloud_user, $sectionIds, true, null, null, true,$request->input("myTask",true));
        return $this->createJsonResponse("ok", false, 200, ["tasks" => $tasks]);
    }

    public static function loadTaskForType($cloudUser, $sectionIds, $directInvation = true, $types = null, $limit = null, $archived = false, $myTask = true)
    {
        if ($directInvation) {
            $ids = DB::table('task_cloud_i_d')->where([
                'cloud_id' => $cloudUser->id,
            ])->pluck('task_id');
        } else {
            $ids = [];
        }

        if ($sectionIds != null) {
            $ids2 = DB::table('task_section')
                ->whereIn('section_id', $sectionIds)
                ->pluck('task_id');
        } else {
            $ids2 = [];
        }

        if($myTask) {
            $id3 = Task::where('cloud_id', '=', $cloudUser->id)->pluck('id'); // Aufgaben, die ich erstellt hab
        } else {
            $id3 = [];
        }



        // alle eigenen plus die anderen, die schon gestartet sind
        $tasks = Task::where(function ($query) use ($ids, $ids2, $id3) {
            $query->where(function ($query) use ($ids) {
                $query->whereIn('id', $ids)->where(function ($subquery) {
                    $now = new \DateTime('NOW');
                    $subquery->where("start", "<=", $now)->orWhereNull("start");;
                });
            })->orWhere(function ($query) use ($ids2) {
                $query->whereIn('id', $ids2)->where(function ($subquery) {
                    $now = new \DateTime('NOW');
                    $subquery->where("start", "<=", $now)->orWhereNull("start");;
                });
            })->orWhere(function ($query) use ($id3) {
                $query->whereIn('id', $id3);
            });
        })->with("attendees")->with("sections")->orderBy('created_at','DESC');
        if ($limit != null) {
            $tasks = $tasks->take($limit);
        }
        $tasks = $tasks->get();

        if($archived)
        {
            $tasks = $tasks->filter(function($task){
                return $task->archived;
            });
            // indizes resetten
            $tasks = collect($tasks->values());
        }
        // archivierte Aufgaben entfernen
        else
        {
            $tasks = $tasks->filter(function($task){
                return !$task->archived;
            });
            // indizes resetten
            $tasks = collect($tasks->values());
        }

        if ($types != null && is_array($types) && count($types) > 0) {
            $filteredList = [];
            foreach ($tasks as $task)
            {
                if(in_array($task->state, $types))
                {
                    $filteredList[] = $task;
                }
            }
            return $filteredList;
        }
        return $tasks;
    }

    /**
     * @OA\Post (
     *     tags={"v1","task"},
     *     path="/api/v1/task/create",
     *     description="Creates a new task",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="start",
     *       required=false,
     *       in="query",
     *       description="start timestamp, optional can be null, if the task is visible now",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="end",
     *       required=false,
     *       in="query",
     *       description="end timestamp, date until the task musst be completed by the students",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="title",
     *       required=false,
     *       in="query",
     *       description="title of the task",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="description",
     *       required=false,
     *       in="query",
     *       description="description of the task",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="type",
     *       required=false,
     *       in="query",
     *       description="type of the task",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="handIn",
     *       required=true,
     *       in="query",
     *       description="no, file, text are supported",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="attendees",
     *       required=true,
     *       in="query",
     *       description="array with cloud ids that are attendees of the task",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="sections",
     *       required=true,
     *       in="query",
     *       description="array with section ids that are attendees of the task",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function createTask(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        if ($request->input("start") && $request->input("start") != "null" && $request->input("end") && $request->input("end") != "null") {
            if (Carbon::parse($request->input("start"))->isAfter(Carbon::parse($request->input("end"))))
                return $this->createJsonResponse("Startdate is after Enddate", true, 400);
        }

        $task = new Task;
        $task->cloud_id = $cloud_user->id;
        $task->title = $request->input("title");
        $task->start = date("Y-m-d H:i", strtotime($request->input("start")));
        $task->end = date("Y-m-d H:i", strtotime($request->input("end")));
        $task->description = $request->input("description", "");
        $task->privatenote = $request->input("privatenote", "");
        $task->handIn = $request->input("handIn", "no");
        $task->remember_minutes = $request->input("remember_minutes", -1);
        $task->type = $request->input("type", "text");


        $task->save();
        if($task->type == "document")
        {
            $submissiontemplate = new SubmissionTemplate();
            $submissiontemplate->task = $task->id;
            $submissiontemplate->save();
            $task->load("submissiontemplate");
        }

        $teilnehmers = $request->input("attendees");
        if ($teilnehmers)
            foreach ($teilnehmers as $teilnehmer) {
                $task->addTeilnehmerById($teilnehmer);
            }

        $gruppes = $request->input("sections");
        if ($gruppes)
            foreach ($gruppes as $gruppe) {
                $task->addSectionById($gruppe);
            }
        $task->load("attendees");
        $task->load("sections");

        return parent::createJsonResponse("Task was created", false, 200, ["task" => $task]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","task"},
     *     path="/api/v1/task/{taskId}",
     *     description="Updates a task",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="taskId",
     *       required=true,
     *       in="path",
     *       description="id of the task",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="start",
     *       required=false,
     *       in="query",
     *       description="start timestamp, optional can be null, if the task is visible now",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="end",
     *       required=false,
     *       in="query",
     *       description="end timestamp, date until the task musst be completed by the students",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="title",
     *       required=false,
     *       in="query",
     *       description="title of the task",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="description",
     *       required=false,
     *       in="query",
     *       description="description of the task",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="type",
     *       required=false,
     *       in="query",
     *       description="type of the task",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="handIn",
     *       required=true,
     *       in="query",
     *       description="no, file, text are supported",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="attendees",
     *       required=true,
     *       in="query",
     *       description="array with cloud ids that are attendees of the task",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="sections",
     *       required=true,
     *       in="query",
     *       description="array with section ids that are attendees of the task",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function updateTask($taskId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = Task::find($taskId);
        if ($task == null)
            return $this->createJsonResponse("Wrong task id supplied", true, 400);

        if ($task->cloud_id != $cloud_user->id) {
            return $this->createJsonResponse("You have no no permission to do this.", true, 400);
        }

        $task->title = $request->input("title");
        $task->start = date("Y-m-d H:i", strtotime($request->input("start")));
        $task->end = date("Y-m-d H:i", strtotime($request->input("end")));
        $task->description = $request->input("description");
        $task->privatenote = $request->input("privatenote");
        $task->handIn = $request->input("handIn");
        $task->remember_minutes = $request->input("remember_minutes", -1);
        // nicht mehr aktualisieren bei bbw
        //$task->type = $request->input("type", "text");

        $task->save();

        $teilnehmers = $request->input("attendees");

        if (is_array($teilnehmers)) {
            // löschen von teilnehmern, die nicht mehr eingeladen sind
            \Illuminate\Support\Facades\DB::table('task_cloud_i_d')->where([
                'task_id' => $task->id,
            ])->whereNotIn('cloud_id', $teilnehmers)->delete();
            // Submission mitlöschen
            \Illuminate\Support\Facades\DB::table('submissions')->where([
                'task_id' => $task->id,
            ])->whereNotIn('cloudid', $teilnehmers)->delete();

            // HInzufügen
            foreach ($teilnehmers as $teilnehmer) {
                $task->addTeilnehmerById($teilnehmer, 0);
            }
        }

        $gruppes = $request->input("sections");
        if (is_array($gruppes)) {
            \Illuminate\Support\Facades\DB::table('task_section')->where([
                'task_id' => $task->id,
            ])->whereNotIn('section_id', $gruppes)->delete();

            // TODO: eig. alle Submission löschen, die jetzt noch aus der Gruppe kommen

            foreach ($gruppes as $gruppe) {
                $task->addSectionById($gruppe);
            }
        }
        $task->load("attendees");
        $task->load("sections");

        return $this->detailsTask($taskId, $request);
    }

    public function finishTaskUpdate($taskId, Request $request)
    {
        // todo finish task update
        $cloud_user = parent::getUserForToken($request);
        $task = Task::find($taskId);
        if ($task == null)
            return $this->createJsonResponse("Wrong task id supplied", true, 400);

        if ($task->cloud_id != $cloud_user->id) {
            return $this->createJsonResponse("You have no no permission to do this.", true, 400);
        }

        $task->finishSetup = true;
        $task->save();

        foreach ($task->attendees as $teilnehmer) {
            FeedObserver::addUserAcitivty($teilnehmer->id, Auth::user(), "App\CloudID", Task::$FEED_CREATE, $task->id, $task);
        }
        foreach ($task->sections as $section) {
            $group = $section->group;
            foreach ($group->members() as $member) {
                if ($section->isAllowed($member, PermissionConstants::EDUCA_SECTION_TASK_RECEIVE)) {
                    FeedObserver::addUserAcitivty($member->id, Auth::user(), "App\CloudID", Task::$FEED_CREATE, $task->id, $task);
                }
            }
        }

        $task->load("attendees");
        $task->load("sections");

        return parent::createJsonResponse("Feedcard for Task are created", false, 200, ["task" => $task]);
    }

    public function saveTaskFormularTemplate($taskId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = Task::find($taskId);
        if ($task == null)
            return $this->createJsonResponse("Wrong task id supplied", true, 400);

        if ($task->cloud_id != $cloud_user->id) {
            return $this->createJsonResponse("You have no no permission to do this.", true, 400);
        }

        if(!$task->formular_id)
        {
            $formular = new Formular;
            $formular->save();
            $task->formular_id  = $formular->id;
            $task->save();
        }

        else
            $formular = Formular::where(["id" => $task->formular_id])->first();
        if(!$formular)
            return $this->createJsonResponse("Form not found. ", true, 400);

           $lastRevision = $formular->lastRevision;

            if($lastRevision->data != $request->input('form_template'))
            {
                $lastRevisionNew = new FormularRevision();
                $lastRevisionNew->data = $request->input('form_template');
                $lastRevisionNew->formular_id = $formular->id;
                $lastRevisionNew->number = $lastRevision->number + 1;
                $lastRevisionNew->user_id = $cloud_user->id;
                $lastRevisionNew->save();
            }

            return $this->detailsTask($taskId, $request);
    }
    /**
     * @OA\Get (
     *     tags={"v1","task"},
     *     path="/api/v1/task/{taskId}/details",
     *     description="Updates a task",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="taskId",
     *       required=true,
     *       in="path",
     *       description="id of the task",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function detailsTask($taskId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = Task::find($taskId);
        if ($task == null)
            return $this->createJsonResponse("Wrong task id supplied", true, 400);

        $task->load("attendees");
        $task->load("sections");
        $task->append("documentCount");
        $form = Formular::where(["id" => $task->formular_id])->first();
        if($form)
            $form->append("lastRevision");
        $task->formular = $form;

        // set seen = true for this cloud user
        foreach ($task->submissions as $submission)
        {
            if($cloud_user->id == $submission->cloudid)
            {
                $submission->has_seen = true;
                $task->is_submission_seen = true;
                $submission->save();
            }
        }

        $task->unsetRelation("submissions");

        if ($cloud_user->id == $task->cloud_id) {
            // ersteller
            $task->allAffectedPersonen();
            if($task->submissiontemplate)
            {
                $task->load("submissiontemplate");
            }
            return parent::createJsonResponse("Task information", false, 200, ["task" => $task, "submissions" => $task->submissions]);
        }
        else
        {
            unset($task->privatenote);
        }

        return parent::createJsonResponse("Task information", false, 200, ["task" => $task, "submission" => $task->einreichungForUser($cloud_user->id)]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","task"},
     *     path="/api/v1/task/{taskId}/delete",
     *     description="Updates a task",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="taskId",
     *       required=true,
     *       in="path",
     *       description="id of the task",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function deleteTask($taskId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = Task::find($taskId);
        $withFeedCard = $request->withFeedCard;
        if ($task == null)
            return $this->createJsonResponse("Wrong task id supplied", true, 400);

        if ($task->cloud_id == $cloud_user->id) {
            $task->delete($withFeedCard);
            return parent::createJsonResponse("Task was deleted", false, 200);
        }
        return $this->createJsonResponse("You have no no permission to do this.", true, 400);
    }

    /**
     * @OA\Get (
     *     tags={"v1","task", "submission"},
     *     path="/api/v1/task/{taskId}/submissions",
     *     description="Submissions of a task",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="taskId",
     *       required=true,
     *       in="path",
     *       description="id of the task",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function submissionListTask($taskId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = Task::find($taskId);
        if ($task == null)
            return $this->createJsonResponse("Wrong task id supplied", true, 400);
        $submissions = $task->submissions;

        return parent::createJsonResponse("Submission list:", false, 200, ["submissions" => $submissions]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","task", "submission"},
     *     path="/api/v1/task/{taskId}/submissions/createText",
     *     description="Submissions of a task",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="taskId",
     *       required=true,
     *       in="path",
     *       description="id of the task",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="stage",
     *       required=false,
     *       in="query",
     *       description="stage of the submission, draft, handed_in, reviewed. Default ist draft",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="description",
     *       required=false,
     *       in="query",
     *       description="optional text of the submission",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function createTextSubmission($taskId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = Task::find($taskId);
        if ($task == null || $task->type != "text")
            return $this->createJsonResponse("Wrong task id supplied", true, 400);
        $submission = new Submission;
        $submission->cloudid = $cloud_user->id;
        $submission->task_id = $task->id;

        $submission->stage = $request->input("stage", "draft");
        $submission->description = $request->input("description");

        $submission->save();
        return parent::createJsonResponse("Submission created", false, 200, ["submission" => $submission]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","task", "submission"},
     *     path="/api/v1/task/{taskId}/submissions/createDocument",
     *     description="Submissions of a task",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="taskId",
     *       required=true,
     *       in="path",
     *       description="id of the task",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="document",
     *       required=true,
     *       in="query",
     *       description="document as submission",
     *         @OA\Schema(
     *           type="file"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function createDocumentSubmission($taskId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = Task::find($taskId);
        if ($task == null || $task->type != "document")
            return $this->createJsonResponse("Wrong task id supplied", true, 400);

        $file = $request->file('document');
        if($file == null)
            return $this->createJsonResponse("no document supplied", true, 400);

        $submission = new Submission;
        $submission->cloudid = $cloud_user->id;
        $submission->task_id = $task->id;

        $submission->stage = "draft";
        $submission->description = ""; // document -> no description text
        $submission->save();

        // store and link supplied document
        $path = $file->store("documents");
        $document = new Dokument();
        $document->name = $file->getClientOriginalName();
        $document->file_type = $file->getClientOriginalExtension();
        $document->size = $file->getSize();
        $document->parent_id = 0;
        $document->owner_id = $cloud_user->id;
        $document->owner_type = "cloudid";
        $document->type = "file";
        $document->disk_name = $path;
        $document->save();


        DB::table('model_dokument')->insert([
            'model_id' => $submission->id,
            'model_type' => "einreichung",
            'dokument_id' => $document->id,
        ]);

        // notifiy in the feed
        $submission->notifiyFeed($document);

        return parent::createJsonResponse("Submission created", false, 200, ["submission" => $submission]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","task", "submission"},
     *     path="/api/v1/task/{taskId}/submissions/createCheck",
     *     description="Submissions of a task",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="taskId",
     *       required=true,
     *       in="path",
     *       description="id of the task",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function createCheckSubmission($taskId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = Task::find($taskId);
        if ($task == null || $task->type != "check")
            return $this->createJsonResponse("Wrong task id supplied", true, 400);

        $submission = new Submission;
        $submission->cloudid = $cloud_user->id;
        $submission->task_id = $task->id;

        $submission->stage = "completed";
        $submission->description = "erledigt";

        $submission->save();
        return parent::createJsonResponse("Submission created", false, 200, ["submission" => $submission]);
    }

    public function createFormSubmission($taskId, Request $request)
    {

    }
    /**
     * @OA\Get (
     *     tags={"v1","task", "submission"},
     *     path="/api/v1/task/{taskId}/submissions/{submissionId}",
     *     description="Submissions of a task",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="taskId",
     *       required=true,
     *       in="path",
     *       description="id of the task",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="submissionId",
     *       required=true,
     *       in="path",
     *       description="id of the submission",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function submissionDetails($taskId, $submissionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = Task::find($taskId);
        if ($task == null)
            return $this->createJsonResponse("Wrong task id supplied", true, 400);

        $submission = Submission::find($submissionId);
        if ($submission == null || $submission->task_id != $task->id)
            return $this->createJsonResponse("Wrong submission id supplied", true, 400);

        return parent::createJsonResponse("Submission list:", false, 200, ["submission" => $submission]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","task", "submission"},
     *     path="/api/v1/task/{taskId}/submissions/{submissionId}",
     *     description="Submissions of a task",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="taskId",
     *       required=true,
     *       in="path",
     *       description="id of the task",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="submissionId",
     *       required=true,
     *       in="path",
     *       description="id of the submission",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="stage",
     *       required=false,
     *       in="query",
     *       description="stage of the submission, draft, handed_in, reviewed. Default ist draft",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="description",
     *       required=false,
     *       in="query",
     *       description="optional text of the submission",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="rating",
     *       required=false,
     *       in="query",
     *       description="optional text for the rating of the task by the teacher",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="points",
     *       required=false,
     *       in="query",
     *       description="optional points for the rating of the task by the teacher",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function updateSubmission($taskId, $submissionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = Task::find($taskId);
        if ($task == null)
            return $this->createJsonResponse("Wrong task id supplied", true, 400);

        $submission = Submission::find($submissionId);
        if ($task == null)
            return $this->createJsonResponse("Wrong submission id supplied", true, 400);

        // er kann nur was ändern, wenn Draft
        if ($submission->stage == "draft") {
            /** STUDENT  */
            if ($request->has("description"))
                $submission->description = $request->input("description");
            if ($request->has("stage")) {
                $submission->stage = $request->input("stage", "draft");
                if ($submission->stage == "review") {
                    // Create submission to feed of the creator
                    FeedObserver::addUserAcitivty($task->cloud_id, $cloud_user, "App\CloudID", Task::$FEED_SUBMITTED, $submission->id, $submission);
                }
            }
        }

        if ($task->cloud_id == $cloud_user->id) {
            /**
             * TEACHER / CREATOR ONLY
             */

            if ($request->has("points"))
                $submission->points = $request->input("points");
            if ($request->has("rating"))
                $submission->rating = $request->input("rating");

            if ($request->has("stage")) {
                $submission->stage = $request->input("stage", "draft");

                if ($submission->stage == "completed") {
                    // Create submission to feed of the creator
                    FeedObserver::addUserAcitivty($submission->cloudid, $cloud_user, "App\CloudID", Task::$FEED_RATED, $submission->id, $submission);
                    $context = [];
                    $interactiveUserData = InteractiveUserData::where("dataId","=",$task->id)->where("cloud_id","=",$submission->cloudid)->first();

                    if($interactiveUserData != null)
                    {
                        $learnContent = LearnContent::where("foreignId","=",$interactiveUserData->subContentId)->where("foreignType","=","task")->first();
                        $context = ["contentId" => $learnContent?->contentId,  "grouping" => ["interactive_course_id" => $interactiveUserData->id, "learn_content_id" => $learnContent->id]];
                    }
                    XAPIBaseController::createStatement($submission->ersteller,$context,["id" => $task->id, "objectType" => "educaTask"],
                    [ "id" => "http://adlnet.gov/expapi/verbs/answered", "display" => [ "en-US" => "answered"]],["score" => ["min" => 0, "max" => $task->maxPoints, "raw" => $submission->points, "scaled" => $task->maxPoints == 0 ? 1 : $submission->points/$task->maxPoints]]);
                }
            }
        }

        $submission->save();

        return parent::createJsonResponse("Submission updated", false, 200, ["submission" => $submission]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","task", "submission"},
     *     path="/api/v1/task/{taskId}/completeSubmissions",
     *     description="complete all submissions of a task",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="taskId",
     *       required=true,
     *       in="path",
     *       description="id of the task",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function completeAllSubmissions($taskId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = Task::find($taskId);
        if ($task == null)
            return $this->createJsonResponse("Wrong task id supplied", true, 400);

        if ($task->cloud_id != $cloud_user->id) {
            return $this->createJsonResponse("Not owner of task", true, 403);
        }

        $submissions = [];
        foreach($task->submissions as $submission)
        {
            $submission->stage = "completed";
            FeedObserver::addUserAcitivty($submission->cloudid, $cloud_user, "App\CloudID", Task::$FEED_RATED, $submission->id, $submission);
            $submission->save();
            $submissions[] = $submission;
        }

        return parent::createJsonResponse("Submissions completed", false, 200, ["submissions" => $submissions]);
    }

    /**
     * @OA\Get (
     *     tags={"v1","task", "feed"},
     *     path="/api/v1/feed/tasks",
     *     description="Initial Task for the feed view",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function taskFeed(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        $groups = $cloud_user->gruppen()->pluck("id"); // all group Ids
        $sectionIds = Section::whereIn('group_id', $groups)->pluck('id');
        $tasks = self::loadTaskForType($cloud_user, $sectionIds, true, ["draft"], 5, false, true);

        return $this->createJsonResponse("ok", false, 200, ["tasks" => $tasks]);
    }

    public function resetSubmission($taskId, $submissionId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = Task::find($taskId);
        if ($task == null)
            return $this->createJsonResponse("Wrong task id supplied", true, 400);

        $submission = Submission::find($submissionId);
        if ($task == null)
            return $this->createJsonResponse("Wrong submission id supplied", true, 400);

        FeedObserver::addUserAcitivty($submission->cloudid, $cloud_user, "App\CloudID", Task::$FEED_RESET, $submission->id, $submission);

        $submission->stage = "draft";
        $submission->save();

        $submissions = [];
        foreach($task->submissions as $submission)
        {
            $submissions[] = $submission;
        }

        return parent::createJsonResponse("Submissions completed", false, 200, ["submissions" => $submissions]);
    }


    public function closeTask($taskId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = Task::find($taskId);
        if ($task == null)
            return $this->createJsonResponse("Wrong task id supplied", true, 400);

        if ($task->cloud_id == $cloud_user->id) {
            foreach($task->submissions as $submission) {
                $submission->stage = "completed";
                $submission->save();
            }
            return $this->detailsTask($taskId, $request);
        }
        return $this->createJsonResponse("error", true, 200, ["task" => $task]);
    }

    public function contentTask($taskId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        $input_file = $request->file("import_file");
        if (!$input_file->isValid())
            return $this->createJsonResponse("File upload was wrong", true, 400);

        $task = Task::find($taskId);
        if ($task == null)
            return $this->createJsonResponse("Wrong task id supplied", true, 400);


        $task->contentId = str_random(32);
        $task->save();

        $request->file("import_file")->storeAs("h5p/tmp", $task->contentId . ".zip");
        $zip = new ZipArchive;
        $res = $zip->open(storage_path("app/h5p/tmp/" . $task->contentId . ".zip"));
        if ($res === TRUE) {
            $zip->extractTo(storage_path("app/h5p/tmp/" . $task->contentId));
            $zip->close();

            $diskContentStorage = new DiskContentStorage();
            $diskContentStorage->addContent($task->contentId);
            $diskContentStorage->addFile($task->contentId, storage_path("app/h5p/tmp/" . $task->contentId . "/h5p.json"), "h5p.json");
            unlink(storage_path("app/h5p/tmp/" . $task->contentId . "/h5p.json"));
            $files = array_diff($this->scanAllDir(storage_path("app/h5p/tmp/" . $task->contentId . "/content")), array('.', '..'));
            foreach ($files as $file) {
                $diskContentStorage->addFile($task->contentId, storage_path("app/h5p/tmp/" . $task->contentId . "/content/" . $file), $file);
            }
            $this->rrmdir(storage_path("app/h5p/tmp/" . $task->contentId . "/content"));


            $diskLibStorage = new DiskLibraryStorage();
            $files = array_diff($this->scanAllDir(storage_path("app/h5p/tmp/" . $task->contentId)), array('.', '..'));
            foreach ($files as $file) {
                $diskLibStorage->addFile(storage_path("app/h5p/tmp/" . $task->contentId . "/" . $file), $file);
            }

            $this->rrmdir(storage_path("app/h5p/tmp/" . $task->contentId));
            unlink(storage_path("app/h5p/tmp/" . $task->contentId . ".zip"));
        } else {
            return $this->createJsonResponse("Not possible to extract the imported file", true, 400);
        }

        return $this->detailsTask($taskId,$request);
    }


    private function recursiveAddToZip($zip, $baseFolder, $folder)
    {
        foreach (Storage::files($baseFolder,true) as $file)
        {
            // skip this file, should be in the main folder
            if(str_replace($baseFolder,"",$file) == "/h5p.json")
                continue;
            $zip->addFromString($folder.str_replace($baseFolder,"",$file),  Storage::get($file));
        }
    }

    private function recursiveAddToZipLib($zip, $baseFolder, $folder)
    {
        foreach (Storage::disk("public")->files($baseFolder,true) as $file)
        {
            $zip->addFromString($folder.str_replace($baseFolder,"",$file),  Storage::disk("public")->get($file));
        }
    }

    private function scanAllDir($dir)
    {
        $result = [];
        foreach (scandir($dir) as $filename) {
            if ($filename[0] === '.') continue;
            $filePath = $dir . '/' . $filename;
            if (is_dir($filePath)) {
                foreach ($this->scanAllDir($filePath) as $childFilename) {
                    $result[] = $filename . '/' . $childFilename;
                }
            } else {
                $result[] = $filename;
            }
        }
        return $result;
    }

    private function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);

            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dir . '/' . $object) == 'dir') {
                        $this->rrmdir($dir . '/' . $object);
                    } else {
                        unlink($dir . '/' . $object);
                    }
                }
            }

            reset($objects);
            rmdir($dir);
        }
    }

    public function aiSubmissions($taskId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = Task::find($taskId);
        if ($task == null)
            return $this->createJsonResponse("Wrong task id supplied", true, 400);

        $correctAnswer = $request->input("correctAnswer");
        $aiResult = [];
        $learnAIController = new LearnAIController();

        foreach ($task->submissions as $submission)
        {
            $singleResult = [];
            $score = $learnAIController->textCompare($correctAnswer, $submission->description);
            $singleResult["submission_id"] = $submission->id;
            $singleResult["score"] = $score;
            $aiResult[] = $singleResult;
        }

        return $this->createJsonResponse("rating done", false, 200, ["aiResult" => $aiResult]);
    }
}
