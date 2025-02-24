<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RIOSSelfServiceController extends ApiController
{
    private function getClientToken()
    {
        $url = config("rios.self_service.url") . "/webservice/connect/token";
        $client_id = config("rios.self_service.client_id");
        $client_secret = config("rios.self_service.client_secret");
        $scope = config("rios.self_service.scope");
        $grant_type = "client_credentials";

        $response = Http::asForm()->post($url, [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'scope' => $scope,
            'grant_type' => $grant_type,
        ]);

        if ($response->failed()) {
            Log::warning("Failed to receive token from RIOS! Status:".$response->status()." Body:".$response->body());
            return null;
        }

        return $response->json()['access_token'] ?? null;
    }

    private function getRIOSUserId()
    {
        return "12345";
    }

    public function executeRIOSCommand(Request $request)
    {
        $token = $this->getClientToken();
        if (!$token) {
            return parent::createJsonResponse("Failed to retrieve client token", true, 500);
        }

        $user_id = $this->getRIOSUserId();
        $url = config("rios.self_service.url") . "/webservice/selfservice";
        $selfservice = $request->input("selfService");
        $content = $request->input("content");

        $response = Http::withToken($token)->post($url, [
            'userid' => $user_id,
            'selfService' => $selfservice,
            'content' => $content,
        ]);

        if ($response->failed()) {
            Log::warning("Failed to execute RIOS command! Status:".$response->status()." Body:".$response->body());
            return parent::createJsonResponse("Failed to execute RIOS command", true, 500);
        }

        $responseData = $response->json();

        return parent::createJsonResponse("RIOS command success", false, 200, ["data" => $responseData]);
    }
}
