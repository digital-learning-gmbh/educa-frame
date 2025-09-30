<?php

namespace App\Http\Controllers\API\V1;

use App\CloudID;
use App\Http\Controllers\API\ApiController;
use App\Schuler;
use App\Services\RIOSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RIOSSelfServiceController extends ApiController
{

    private function getRIOSUserId(CloudID $cloudID)
    {
        $shculer_id = $cloudID->getAppLogin("klassenbuch");
        if($shculer_id && Schuler::find($shculer_id)){
            return Schuler::find($shculer_id)->external_booking_id;
        }
        return null;
    }

    public function executeRIOSCommand(Request $request)
    {
        $service = new RIOSService();
        $token = $service->getClientToken();
        if (!$token) {
            return parent::createJsonResponse("Failed to retrieve client token", true, 500, null, 503);
        }

        $cloud_user = parent::getUserForToken($request);

        $user_id = $this->getRIOSUserId($cloud_user);
        $url = config("rios.self_service.url") . "/webservice/selfservice";
        $selfservice = $request->input("selfService");
        $content = $request->input("content");

        $response = Http::withToken($token)->post($url, [
            'userid' => $user_id,
            'selfService' => $selfservice,
            'content' => $content,
        ]);

        if ($response->failed()) {
            Log::warning("Failed to execute RIOS command! Status: " . $response->status() . " Body: " . $response->body());
            return parent::createJsonResponse("Failed to execute RIOS command", true, $response->status(), ["data" => $response->body()], $response->status());
        }

        $responseData = $response->json();

        return parent::createJsonResponse("RIOS command success", false, 200, ["data" => $responseData]);
    }


    public function bridgeJs(Request $request)
    {
        $service = new RIOSService();
        $token = $service->getClientToken();
        if (!$token) {
            return parent::createJsonResponse("Failed to retrieve client token", true, 500, null, 503);
        }

        $jsFile = $request->input("jsFile");

        if (!$jsFile || preg_match('/[^\w\-\.]/', $jsFile)) {
            return parent::createJsonResponse("Invalid jsFile parameter", true, 400);
        }

        $url = rtrim(config("rios.self_service.url"), '/') . "/webservice/selfservice/widget/" . $jsFile;

        try {
            $response = Http::withToken($token)->get($url);

            if (!$response->successful()) {
                return parent::createJsonResponse("Failed to fetch JS file", true, $response->status());
            }

            return response($response->body(), 200)
                ->header('Content-Type', 'application/javascript');

        } catch (\Exception $e) {
            return parent::createJsonResponse("Exception: " . $e->getMessage(), true, 500);
        }
    }
}
