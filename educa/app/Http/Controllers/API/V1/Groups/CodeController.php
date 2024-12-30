<?php


namespace App\Http\Controllers\API\V1\Groups;


use App\AccessCode;
use App\Http\Controllers\API\ApiController;
use App\Section;
use Illuminate\Http\Request;


class CodeController extends ApiController
{

    public function getCode($groupId, Request $request)
    {
        if(!$groupId)
            return $this->createJsonResponse("Group id not given in request.", true, 400);


        $code = AccessCode::where('model_id','=', $groupId)->where("type","=","group")->first();
        if($code == null)
        {
            $code = new AccessCode;
            $code->model_id = $groupId;
            $code->type = "group";
            $codeString = str_random("6");
            while (AccessCode::where('code', '=', $code)->first() != null)
            {
                $codeString = str_random("6");
            }
            $code->code = $codeString;
            $code->save();
        }

        return $this->createJsonResponse("ok", false,200, ["code" => $code] );
    }
}
