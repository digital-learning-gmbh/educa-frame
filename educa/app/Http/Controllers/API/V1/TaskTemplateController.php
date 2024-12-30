<?php
namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\V1\xAPI\XAPIBaseController;
use App\InteractiveCourse;
use App\Models\TaskTemplateSubmissionTemplate;
use App\Observers\FeedObserver;
use App\Section;
use App\SubmissionTemplate;
use App\Models\InteractiveUserData;
use App\Models\LearnContent;
use App\Task;
use App\TaskTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use StuPla\CloudSDK\formular\models\Formular;
use StuPla\CloudSDK\formular\models\FormularRevision;

class TaskTemplateController extends ApiController
{
    /**
     * @OA\Post (
     *     tags={"v1","task"},
     *     path="/api/v1/tasktemplates",
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
     *     @OA\Response(response="200", description="")
     * )
     */
    public function getTaskTemplates(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        $taskTemplates = TaskTemplate::where("cloud_id", "=", $cloud_user->id)->where("isLearnContent","=",false)->get();

        foreach ($taskTemplates as $taskTemplate)
        {
            $form = Formular::where(["id" => $taskTemplate->formular_id])->first();
            if($form)
                $form->append("lastRevision");
            $taskTemplate->formular = $form;
        }

        return $this->createJsonResponse("ok", false, 200, ["taskTemplates" => $taskTemplates]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","task"},
     *     path="/api/v1/tasktemplates/create",
     *     description="Creates a new task template",
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
     *     @OA\Response(response="200", description="")
     * )
     */
    public function createTaskTemplate(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        if (empty($request->input("title")) || ctype_space($request->input("title")))
            return $this->createJsonResponse("No title supplied.", true, 400);

        $privatenote = $request->input("privatenote", "");
        if (empty($privatenote) || ctype_space($privatenote))
            $privatenote = "";

        $task = new TaskTemplate;
        $task->cloud_id = $cloud_user->id;
        $task->title = $request->input("title");
        $task->description = $request->input("description", "");
        $task->privatenote = $privatenote;
        $task->handIn = $request->input("handIn", "no");
        $task->type = $request->input("type", "text");
        $task->isLearnContent = $request->input("isLearnContent", false);
        $task->defaultEndOffset = $request->input("defaultEndOffset", -1);
        $task->autostart = $request->input("autostart", false);
        $task->maxPoints = $request->input("maxPoints", 100);

        $task->save();
        if($task->type == "document")
        {
            $submissiontemplate = new TaskTemplateSubmissionTemplate();
            $submissiontemplate->task_template_id = $task->id;
            $submissiontemplate->save();
            $task->load("submissiontemplate");
        }

        return parent::createJsonResponse("Task template was created", false, 200, ["taskTemplate" => $task]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","task"},
     *     path="/api/v1/tasktemplates/{taskId}/update",
     *     description="Updates a task template",
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
     *       description="id of the task template",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="title",
     *       required=false,
     *       in="query",
     *       description="title of the task template",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="description",
     *       required=false,
     *       in="query",
     *       description="description of the task template",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="type",
     *       required=false,
     *       in="query",
     *       description="type of the task template",
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
     *     @OA\Response(response="200", description="")
     * )
     */
    public function updateTaskTemplate($taskTemplateId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = TaskTemplate::find($taskTemplateId);
        if ($task == null)
            return $this->createJsonResponse("Wrong task template id supplied", true, 400);

        if ($task->cloud_id != $cloud_user->id && $task->isLearnContent == 0) {
            return $this->createJsonResponse("You have no no permission to do this.", true, 400);
        }

        $task->title = $request->input("title");
        $task->description = $request->input("description");
        $task->privatenote = $request->input("privatenote");
        $task->handIn = $request->input("handIn");
        $task->type = $request->input("type", "text");
        $task->isLearnContent = $request->input("isLearnContent", false);
        $task->defaultEndOffset = $request->input("defaultEndOffset", -1);
        $task->autostart = $request->input("autostart", false);
        $task->maxPoints = $request->input("maxPoints", 100);

        $task->save();

        if($task->type == "document")
        {
            $submissiontemplate = TaskTemplateSubmissionTemplate::where("task_template_id","=",$task->id)->first();
            if($submissiontemplate == null)
                $submissiontemplate = new TaskTemplateSubmissionTemplate();
            $submissiontemplate->task_template_id = $task->id;
            $submissiontemplate->save();
            $task->load("submissiontemplate");
        }

        return $this->getTaskTemplate($taskTemplateId, $request);
    }

    /**
     * @OA\Post (
     *     tags={"v1","task"},
     *     path="/api/v1/tasktemplates/{taskId}/delete",
     *     description="deletes a task template",
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
     *       description="id of the task template",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function deleteTaskTemplate($taskTemplateId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = TaskTemplate::find($taskTemplateId);
        if ($task == null)
            return $this->createJsonResponse("Wrong task template id supplied", true, 400);

        if ($task->cloud_id != $cloud_user->id) {
            return $this->createJsonResponse("You have no no permission to do this.", true, 400);
        }

        foreach($task->dokumente(0) as $tlDocument)
        {
            $tlDocument->delete();
        }

        $task->delete();

        return parent::createJsonResponse("Task template was deleted", false, 200, []);
    }

    public function createTaskFromTemplate($taskTemplateId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        $template = TaskTemplate::find($taskTemplateId);
        if ($template == null)
            return $this->createJsonResponse("Wrong task template id supplied", true, 400);

        if ($template->cloud_id != $cloud_user->id) {
            return $this->createJsonResponse("You have no no permission to do this.", true, 400);
        }

        $task = new Task;

        $task->cloud_id = $cloud_user->id;
        $task->title = $template->title;
        $task->start = date("Y-m-d H:i");
        $task->end = date("Y-m-d H:i",strtotime("+1 week"));
        $task->description = $template->description;
        $task->privatenote = $template->privatenote;
        $task->handIn = $template->handIn;
        $task->remember_minutes = -1;
        $task->type = $template->type;
        $task->maxPoints = $template->privatenote;

        $task->save();

        // recursively duplicate documents
        $ids = [];
        foreach($template->dokumente(0) as $document)
        {
            $ids = array_merge($ids, $document->duplicate($document->owner_id,$document->parent_id));
        }

        $relations = [];
        foreach ($ids as $id)
        {
            $relations[] = ["model_id" => $task->id, "model_type" => "task", "dokument_id" => $id];
        }

        if($task->type == "document")
        {
            $submissiontemplate = new SubmissionTemplate();
            $submissiontemplate->task = $task->id;
            $submissiontemplate->save();
            $task->load("submissiontemplate");

            $ids = [];
            if($template->submissiontemplate != null) {
                foreach ($template->submissiontemplate->dokumente(0) as $document) {
                    $ids = array_merge($ids, $document->duplicate($document->owner_id, $document->parent_id));
                }
                foreach ($ids as $id) {
                    $relations[] = ["model_id" => $submissiontemplate->id, "model_type" => "submission_template", "dokument_id" => $id];
                }
            }
        }
        DB::table('model_dokument')->insert($relations);

        if($task->type == "form")
        {
            $formular = new Formular;
            $formular->save();
            $task->formular_id  = $formular->id;
            $task->save();

            $form = Formular::where(["id" => $template->formular_id])->first();
            if($form != null && $form->getLastRevisionAttribute() != null) {
                $lastRevisionNew = new FormularRevision();
                $lastRevisionNew->data = $form->getLastRevisionAttribute()->data;
                $lastRevisionNew->formular_id = $formular->id;
                $lastRevisionNew->number = 1;
                $lastRevisionNew->user_id = $cloud_user->id;
                $lastRevisionNew->save();
            }
        }



        return parent::createJsonResponse("Task was created from template", false, 200, ["task" => $task]);
    }

    public function getTaskTemplate($taskTemplateId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $taskTemplate = TaskTemplate::find($taskTemplateId);
        $taskTemplate->load("submissiontemplate");

        $form = Formular::where(["id" => $taskTemplate->formular_id])->first();
        if($form)
            $form->append("lastRevision");
        $taskTemplate->formular = $form;

        return $this->createJsonResponse("ok", false, 200, ["taskTemplate" => $taskTemplate]);
    }

    public function interactiveCourseCreate($taskTemplateId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $template = TaskTemplate::find($taskTemplateId);

        if ($template == null)
            return $this->createJsonResponse("Wrong task template id supplied", true, 400);


        $template->load("submissiontemplate");
        $interactiveCourse = InteractiveCourse::find($request->input("interactiveCourseId"));
        if ($interactiveCourse == null)
            return $this->createJsonResponse("interactive course null", true, 400);

        $section = Section::find($request->input("sectionId"));

        $course_responsible_id = null;

        if($section != null) {
            $sectionInfo = DB::table("interactive_course_section")->where("section_id","=",$section->id)->where("interactive_course_id","=",$interactiveCourse->id)->first();
            if($sectionInfo != null)
            {
                $course_responsible_id = $sectionInfo->responsible_cloud_id;
            }
        }
        if($course_responsible_id == null)
        {
            $course_responsible_id = $interactiveCourse->course_responsible_id;
        }

        if ($course_responsible_id == null)
            return $this->createJsonResponse("You need to set some responsible for this course", true, 400);

        $learnContent = LearnContent::find($request->input("learnContentId"));

        if ($learnContent == null)
            return $this->createJsonResponse("You need to set an learnContentId", true, 400);

        $task = new Task;

        $task->cloud_id = $course_responsible_id;
        $task->title = $template->title;
        $task->start = date("Y-m-d H:i");
        if($template->defaultEndOffset >= 0) {
            $task->end = date("Y-m-d H:i", strtotime("+".$template->defaultEndOffset." minutes"));
        }
        $task->description = $template->description;
        $task->privatenote = $template->privatenote;
        $task->handIn = $template->handIn;
        $task->remember_minutes = -1;
        $task->type = $template->type;
        $task->finishSetup = true;
        $task->maxPoints = $template->maxPoints;

        $task->save();

        $ids = [];
        foreach($template->dokumente(0) as $document)
        {
            $ids = array_merge($ids, $document->duplicate($document->owner_id,$document->parent_id));
        }

        $relations = [];
        foreach ($ids as $id)
        {
            $relations[] = ["model_id" => $task->id, "model_type" => "task", "dokument_id" => $id];
        }

        if($task->type == "document")
        {
            $submissiontemplate = new SubmissionTemplate();
            $submissiontemplate->task = $task->id;
            $submissiontemplate->save();
            $task->load("submissiontemplate");

            $ids = [];
            if($template->submissiontemplate != null) {
                foreach ($template->submissiontemplate->dokumente(0) as $document) {
                    $ids = array_merge($ids, $document->duplicate($document->owner_id, $document->parent_id));
                }
                foreach ($ids as $id) {
                    $relations[] = ["model_id" => $submissiontemplate->id, "model_type" => "submission_template", "dokument_id" => $id];
                }
            }
        }
        DB::table('model_dokument')->insert($relations);

        $task->addTeilnehmerById($cloud_user->id);

        foreach ($task->attendees as $teilnehmer) {
            FeedObserver::addUserAcitivty($teilnehmer->id, Auth::user(), "App\CloudID", Task::$FEED_CREATE, $task->id, $task);
        }

        if($task->type == "form")
        {
            $formular = new Formular;
            $formular->save();
            $task->formular_id  = $formular->id;
            $task->save();

            $form = Formular::where(["id" => $template->formular_id])->first();
            if($form != null && $form->getLastRevisionAttribute() != null) {
                $lastRevisionNew = new FormularRevision();
                $lastRevisionNew->data = $form->getLastRevisionAttribute()->data;
                $lastRevisionNew->formular_id = $formular->id;
                $lastRevisionNew->number = 1;
                $lastRevisionNew->user_id = $cloud_user->id;
                $lastRevisionNew->save();
            }
        }

        XAPIBaseController::createStatement($cloud_user,["contentId" => $learnContent->contentId, "grouping" => ["interactive_course_id" => $interactiveCourse->id, "learn_content_id" => $learnContent->id]],
        ["id" => $task->id, "objectType" => "educaTask"],[ "id" => "http://adlnet.gov/expapi/verbs/attempted", "display" => [ "en-US" => "attempted"]]);

        $dataentry = new InteractiveUserData();
        $dataentry->contentId = $interactiveCourse->id;
        $dataentry->dataId = $task->id;
        $dataentry->cloud_id = $cloud_user->id;
        $dataentry->subContentId = $learnContent->foreignId;
        $dataentry->data = $task->einreichungForUser($cloud_user->id)->id;
        $dataentry->save();

        $task->load("attendees");
        $task->load("sections");
        $task->append("documentCount");
        unset($task->privatenote);

        return $this->createJsonResponse("ok", false, 200, ["taskTemplate" => $template, "task" => $task, "submission" => $task->einreichungForUser($cloud_user->id) ]);
    }


    public function interactiveCourseInformation($taskTemplateId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $template = TaskTemplate::find($taskTemplateId);
        $template->load("submissiontemplate");

        if ($template == null)
            return $this->createJsonResponse("Wrong task template id supplied", true, 400);

        $interactiveCourse = InteractiveCourse::find($request->input("interactiveCourseId"));

        if ($interactiveCourse == null)
            return $this->createJsonResponse("You need a course", true, 400);

        $learnContent = LearnContent::find($request->input("learnContentId"));

        if ($learnContent == null)
            return $this->createJsonResponse("You need to set an learnContentId", true, 400);

        $interactiveCourse = InteractiveUserData::where("cloud_id", "=", $cloud_user->id)
            ->where("contentId", "=", $interactiveCourse->id)
            ->where("subContentId", "=", $learnContent->foreignId)->first();
        if ($interactiveCourse == null) {
            return $this->createJsonResponse("ok", false, 200, ["taskTemplate" => $template]);
        } else {
            $task = Task::find($interactiveCourse->dataId);
            if($task == null) {
                return $this->createJsonResponse("ok", false, 200, ["taskTemplate" => $template]);
            }
            unset($task->privatenote);
            $task->load("attendees");
            $task->load("sections");
            $task->append("documentCount");
            return $this->createJsonResponse("ok", false, 200, ["taskTemplate" => $template, "task" => $task, "submission" =>  $task->einreichungForUser($cloud_user->id) ]);
        }
    }

    public function saveTaskFormularTemplate($taskTemplateId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $task = TaskTemplate::find($taskTemplateId);
        if ($task == null)
            return $this->createJsonResponse("Wrong task template id supplied", true, 400);

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

        return $this->getTaskTemplate($taskTemplateId, $request);
    }

}
