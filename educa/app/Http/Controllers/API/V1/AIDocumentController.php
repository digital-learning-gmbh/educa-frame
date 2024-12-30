<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use App\Models\DokumentParts;
use App\Models\VectorQuery;
use App\Section;
use DigitalLearningGmbh\EducaAiConnector\EducaAIAPIFactory;
use Illuminate\Http\Request;

class AIDocumentController extends ApiController
{
    public function askModel(Request $request)
    {
        $user = parent::getUserForToken($request);

        $model_id = $request->input("model_id");
        $model_type = $request->input("model_type");
        if($model_type != "section")
            return "nout spuuuotted";
        $section = Section::find($model_id);

        $query = $request->input("query");
        $aiFactory = new EducaAIAPIFactory(config("educa-ai.backend"));
        $vectorQuery = new VectorQuery();
        $vectorAPI = $aiFactory->getVectorAPI();
        $vectorQuery->cloud_id = $user->id;
        $vectorQuery->index_name = $section->vector_index;
        $vectorQuery->query = $query;
        $result = $vectorAPI->queryIndex($query,$section->vector_index);
        $vectorQuery->result = $result;
        $vectorQuery->save();

        $parts = [];
        if(array_key_exists("sources",$result))
        {
            foreach ($result["sources"] as $document_id => $score) {
                $dokumentParts = DokumentParts::where("vectorId", "=", $document_id)->with("document")->first();
                if ($dokumentParts != null) {
                    $dokumentParts->score = $score;
                    $parts[] = $dokumentParts;
                }
            }
        }

        return parent::createJsonResponse("query executed",false,200,["result" => $result, "parts" => $parts, "vectorQuery" => $vectorQuery]);
    }
}
