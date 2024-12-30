<?php

namespace App\Http\Controllers\API\V1;

use App\BrowserlessPDF;
use App\DownloadCache;
use App\ExamExecutionDate;
use App\FormularTemplate;
use App\FormularTemplateType;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\V1\Administration\Widgets\TeacherCurriculaWidget;
use App\Http\Controllers\API\V1\Template\TemplateGenerator;
use App\Http\Controllers\API\V1\Template\Templates\BasicExampleTemplate;
use App\Http\Controllers\API\V1\Template\Templates\IBAAnwesenheitExam;
use App\Http\Controllers\API\V1\Template\Templates\IBAAnwesenheitsliste;
use App\Http\Controllers\API\V1\Template\Templates\IBAErgebnisModul;
use App\Http\Controllers\API\V1\Template\Templates\IBAExMaStandardTemplate;
use App\Http\Controllers\API\V1\Template\Templates\IBASOZMAbschlussTemplate;
use App\Http\Controllers\API\V1\Template\Templates\IBAStudienbescheinigungBafoegTemplate;
use App\Http\Controllers\API\V1\Template\Templates\IBAStudienbescheinigungStandardTemplate;
use App\Http\Controllers\API\V1\Template\Templates\IBATorTemplate;
use App\Http\Controllers\API\V1\Template\Templates\IBAVordruckNoten;
use App\Lehrer;
use App\LessonPlan;
use App\ModulExamExecution;
use App\Schuler;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use StuPla\CloudSDK\formular\models\Formular;

class FormularTemplateController extends ApiController
{


    public function getForms(Request $request)
    {
        $templates = FormularTemplate::where('schule_id', '=', $request->input("school_id"));
        if($request->has("model_type"))
        {
            $form_type = FormularTemplateType::where('key','=',$request->input("model_type"))->first();
            if(!$form_type)
                return parent::createJsonResponse("empty",false, 200,["templates" => [] ]);
            $templates = $templates->where('formular_template_type_id','=',$form_type->id);
        }
        $templates = $templates->get();
        return parent::createJsonResponse("ok",false, 200,["templates" => $templates ]);
    }
    /**
     * @OA\Post (
     *     tags={"v1","groups","document"},
     *     path="/api/v1/formtemplates",
     *     description="",
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
     *       name="name",
     *       required=true,
     *       in="query",
     *       description="template name",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="html",
     *       required=true,
     *       in="query",
     *       description="template html",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Create a new form template")
     * )
     */
    public function createFormularTemplate(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $html = $request->input("html");
        $name = $request->input("name");

        if($html == "")
            return $this->createJsonResponse("No template data supplied.", true, 400);
        if($name == "")
            return $this->createJsonResponse("No template name supplied.", true, 400);

        $template = new FormularTemplate();
        $template->name = $name;
        $template->template = $html;
        $template->save();

        return $this->createJsonResponse("template created.", false, 200, ["template" => $template]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","document"},
     *     path="/api/v1/formtemplates/{id}",
     *     description="",
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
     *       name="id",
     *       required=true,
     *       in="path",
     *       description="template id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="name",
     *       required=true,
     *       in="query",
     *       description="template name",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="html",
     *       required=true,
     *       in="query",
     *       description="template html",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Update a template's data")
     * )
     */
    public function updateFormularTemplate($id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $template = FormularTemplate::findOrFail($id);

        $templateData = $request->input("template");
        $template->template = $templateData["template"];
        $template->name = $templateData["name"];

        $template->save();

        return $this->createJsonResponse("template updated.", false, 200, ["template" => $template]);
    }

    public function getFormularTemplate($id, Request $request) {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $template = FormularTemplate::findOrFail($id);
        return $this->createJsonResponse("template info.", false, 200, ["template" => $template]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","document"},
     *     path="/api/v1/formtemplates/{id}/delete",
     *     description="",
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
     *       name="id",
     *       required=true,
     *       in="path",
     *       description="template id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Create and upload a new document")
     * )
     */
    public function deleteFormularTemplate($id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }

        $template = FormularTemplate::findOrFail($id);

        $template->delete();

        return $this->createJsonResponse("template deleted.", false, 200, []);
    }

    /**
     * @OA\Post (
     *     tags={"v1","groups","document"},
     *     path="/api/v1/formtemplates/{id}/print",
     *     description="",
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
     *       name="id",
     *       required=true,
     *       in="path",
     *       description="template id",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="filename",
     *       required=true,
     *       in="query",
     *       description="file name for resulting pdf",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="object",
     *       required=true,
     *       in="query",
     *       description="template data",
     *         @OA\Schema(
     *           type="object"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Retrieve printable PDF of a filled template")
     * )
     */
    public function printFormularTemplate($id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null)
        {
            return $this->createJsonResponse("This token is not valid.", true, 400);
        }
        $object = $request->object;
        if(!$object)
            return $this->createJsonResponse("No object given",true, 400);

        $filename = $request->input("filename");
        if($filename == "")
            return $this->createJsonResponse("No filename given",true, 400);

        $template = FormularTemplate::findOrFail($id);
        $html = $template->fill_json($object);

        $pdf = new BrowserlessPDF();
        $pdf->loadHtml($html);


        return $pdf->download($filename);
    }

//    public function generateFormularTemplate($id, Request $request)
//    {
//        $cloud_user = parent::getUserForToken($request);
//        if($cloud_user == null)
//        {
//            return $this->createJsonResponse("This token is not valid.", true, 400);
//        }
//
//        $model_type = $request->input("model_type");
//        $model_id = $request->input("model_id");
//        $template_id = $id;
//        $template = FormularTemplate::find($template_id);
//
//        if($model_type != $template->template_type->key)
//            return $this->createJsonResponse("The template is not for this model_type", true, 400);
//
//        $object_under_investigation = null;
//        if($model_type == "student")
//        {
//            $object_under_investigation = Schuler::find($model_id);
//        } else if($model_type == "teacher")
//        {
//            $object_under_investigation = Lehrer::find($model_id);
//        }
//
//        if($object_under_investigation == null)
//        {
//            return $this->createJsonResponse("This template has no valid object_under_investigation", true, 400);
//        }
//
//        $html = $template->fill_json($object_under_investigation->getTemplateJSON());
//
//        $pdf = new BrowserlessPDF();
//        $pdf->loadHtml($html);
//
//        $filename = "report.pdf";
//
//        return $pdf->download($filename);
//    }


    public function generateFormularTemplate($id, Request $request)
    {
        // TODO Refactor
        $templateClass = null;
        if($id == 1)
        {
            $templateClass = new IBATorTemplate();
        } else if($id == 2)
        {
            $templateClass = new IBAStudienbescheinigungBafoegTemplate();
        } else if($id == 3)
        {
            $templateClass = new IBAStudienbescheinigungStandardTemplate();
        } else if($id == 4)
        {
            $templateClass = new IBAExMaStandardTemplate();
        }  else if($id == 5)
        {
            $templateClass = new IBAAnwesenheitsliste();
        } else if($id == 6)
        {
            $templateClass = new IBASOZMAbschlussTemplate();
        } else if($id == 7)
        {
            $templateClass = new IBAErgebnisModul();
        } else if($id == 8)
        {
            $templateClass = new IBAErgebnisModul(false);
        } else if($id == 9)
        {
            $templateClass = new IBAVordruckNoten();
        } else if($id == 10)
        {
            $templateClass = new IBAVordruckNoten(false);
        } else if($id == 11)
        {
            $templateClass = new IBAAnwesenheitExam();
        } else if($id == 12)
        {
            $templateClass = new IBAAnwesenheitExam(false);
        }


        $model = null;
        if($request->input("model_type") == "student")
        {
            $model = Schuler::find($request->input("model_id"));
        }
        if($request->input("model_type") == "lessonPlan")
        {
            $model_id2 = explode("_",$request->input("model_id"));
            $datum = $model_id2[2].".".$model_id2[3].".".$model_id2[4];
            $model = ["lessonPlan" => LessonPlan::find($model_id2[1]), "date" => Carbon::parse($datum)];
        }
        if($request->input("model_type") == "grades")
        {
            $model = ExamExecutionDate::find($request->input("model_id"));
        }
        if($request->input("model_type") == "exam_execution")
        {
            $model = ModulExamExecution::find($request->input("model_id"));
        }

        if($model == null || $templateClass == null)
            return $this->createJsonResponse("No model given",true, 400);

        $template = new TemplateGenerator();
        $template->setModel($model);
        $template->setTemplateClass($templateClass);
        $path = $template->generate("pdf");

        $download_cache = new DownloadCache();
        $download_cache->name = "Template";
        $download_cache->filename = "transcripts/".$path;
        $download_cache->cloudid = parent::user()->id;
        $download_cache->downloads = 0;
        $download_cache->token = str_random(128);

        $download_cache->save();

        return $this->createJsonResponse("template generated", false, 200, ["download_cache" => $download_cache]);
    }

    public function openFormular(Request $request)
    {
        $cache = DownloadCache::/*where("id","=",$request->input("id"))->*/where("token","=",$request->input("token"))->first();
        if($cache == null)
            return $this->createJsonResponse("Invalid token or download was deleted",true, 400);

        $cache->downloads++;
        $cache->save();

        return Storage::download($cache->filename);
    }
}
