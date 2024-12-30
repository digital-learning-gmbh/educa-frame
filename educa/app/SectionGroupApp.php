<?php

namespace App;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Shared\RocketChatProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SectionGroupApp extends Model
{

    protected $with = ['groupApp'];

    public function groupApp()
    {
        return $this->belongsTo('App\GroupApp');
    }

    public function section()
    {
        return $this->belongsTo('App\Section');
    }

    public function delete()
    {
        $groupApp = $this->groupApp;
        $section = $this->section;
        switch ($groupApp->type)
        {
            case "announcement":
                $beitrags = Beitrag::where('app_id','=',$this->id)->get();
                foreach($beitrags as $beitrag) $beitrag->delete();
                break;

            case "chat":
                try {
                    $params = json_decode($this->parameters, TRUE);
                    if( !$params["roomId"] )
                        RocketChatProvider::removeGroup(ApiController::user(), $params["roomId"]);
                } catch (\Exception $exception)
                {
                    Log::warning("Could not delete group on chat server: ".$exception->getTraceAsString());
                }
                break;

            case "calendar":
                DB::table('appointment_section')
                    ->where('section_id','=', $section->id)
                    ->delete();
                break;

            case "task":
                DB::table('task_section')
                    ->where('section_id','=', $section->id)
                    ->delete();
                break;

            case "files":
                $ids = DB::table('model_dokument')
                    ->where('model_id', '=', $section->id)
                    ->where('model_type', '=', 'section')
                    ->pluck('dokument_id')->toArray();
                $documents = Dokument::where('parent_id', '=', '0')->whereIn('id', $ids)->get();
                foreach($documents as $document) $document->delete();
                break;

            case "wiki":
                DB::table('educa_wiki_pages')
                    ->where('section_id','=', $section->id)
                    ->delete();

            case "h5pCourse":
                DB::table('interactive_course_section')
                    ->where('section_id','=', $section->id)
                    ->delete();

            case "accessCode": #wird wenn dann auf Gruppenebene gelöscht
            default:
                break;
        }
        return parent::delete();
    }
}
