<?php

namespace App\Http\Controllers\API\V1;

use App\CloudID;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\RocketChatProvider;
use App\Models\AccountRecoveryOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use stdClass;

class RecoveryController extends ApiController
{
    public function getOptions(Request $request)
    {
        $email = $request->input("email");
        $cloudUser = CloudID::where("email","=",$email)->first();
        if($cloudUser == null)
        {
            return parent::createJsonResponse("no options found or user ",false, 200, ["recoverOptions" => null]);
        }
        $securitySettings = AccountRecoveryOption::where("cloud_id","=",$cloudUser->id)->first();
        if($securitySettings == null || (!$securitySettings->emailRecover && !$securitySettings->secondEmailRecover && !$securitySettings->questionRecover))
        {
            return parent::createJsonResponse("no options found or user ",false, 200, ["recoverOptions" => null]);
        }

        $secureCloneObject = new stdClass();
        $secureCloneObject->emailRecover = $securitySettings->emailRecover;
        $secureCloneObject->secondEmailRecover = $securitySettings->secondEmailRecover;
        $secureCloneObject->questionRecover = $securitySettings->questionRecover;
        $secureCloneObject->firstQuestion = $securitySettings->firstQuestion;
        $secureCloneObject->secondQuestion = $securitySettings->secondQuestion;

        return parent::createJsonResponse("no options found or user ",false, 200, ["recoverOptions" => $secureCloneObject]);
    }

    public function sendMail(Request $request)
    {
        $email = $request->input("email");
        $cloudUser = CloudID::where("email","=",$email)->first();
        if($cloudUser == null) {
            return parent::createJsonResponse("email send", false, 200);
        }
        $securitySettings = AccountRecoveryOption::where("cloud_id","=",$cloudUser->id)->first();
        if($securitySettings == null)
        {
            return parent::createJsonResponse("email send", false, 200);
        }
        $option = $request->input("option");

        return parent::createJsonResponse("email send", false, 200);
    }

    public function executeOption(Request $request)
    {
        $email = $request->input("email");
        $cloudUser = CloudID::where("email","=",$email)->first();
        if($cloudUser == null) {
            return parent::createJsonResponse("error", false, 200,["hasError" => true]);
        }
        $securitySettings = AccountRecoveryOption::where("cloud_id","=",$cloudUser->id)->first();
        if($securitySettings == null)
        {
            return parent::createJsonResponse("error", false, 200,["hasError" => true]);
        }

        $option = $request->input("option");
        if($option == "question" && $securitySettings->questionRecover)
        {
            $firstAnswer = $request->input("firstAnswer");
            $secondAnswer = $request->input("secondAnswer");
            if(trim($firstAnswer) == trim($securitySettings->firstAnswer) && trim($secondAnswer) == trim($securitySettings->secondAnswer))
            {
                return $this->loginUser($cloudUser);
            }
        }

        if(($option == "primaryEmail" && $securitySettings->emailRecover) || ($option == "secondaryEmail" && $securitySettings->secondEmailRecover)
            ) {
            $code = $request->input("code");
            if (false) {
                return $this->loginUser($cloudUser);
            }
        }

        return parent::createJsonResponse("error", false, 200,["hasError" => true]);
    }

    public function resetPassword(Request $request)
    {
       $cloudUser = parent::getUserForToken($request);
        if($cloudUser == null) {
            return parent::createJsonResponse("error", true, 200,["hasError" => true]);
        }

        $cloudUser->password = bcrypt($request->input("password"));
        $cloudUser->save();

        return parent::createJsonResponse("error", false, 200,["passwordChange" => true]);
    }


    private function loginUser($cloudUser)
    {
        if (! $token = Auth::guard('api')->login($cloudUser)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $rcUser =  RocketChatProvider::login($cloudUser);
        $resp =  ApiController::createJsonResponseStatic("Login war erfolgreich",false, 200,
            ["user" => Auth::user(),
                "token" => $token,
                'expires_in' => Auth::guard('api')->factory()->getTTL() * 60 ]);
        //$resp->withCookie(cookie( "token", $token, 999999999999,"/",null,false,false));
        Session::put("jwt_token", $token);
        if( $rcUser )
        {
            $resp->withCookie(cookie( "educa_rc_token", $rcUser->getAuthToken(), 999999999999,"/",null,false,false));
            $resp->withCookie(cookie( "educa_rc_uid",  $rcUser->getId(), 999999999999,"/",null,false,false));
        }
        return $resp;
    }
}
