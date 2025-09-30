<?php

namespace App\Services;

use App\Schuler;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RIOSService
{
    public function getClientToken()
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

    public function getTeilnehmer($email)
    {
        try {
        $token = $this->getClientToken();
        $url = rtrim(config("rios.self_service.url"), '/') . "/webservice/selfservice/teilnehmer?email=" . $email ;

            $response = Http::withToken($token)->get($url);

            if (!$response->successful()) {
                return null;
            }

            $id = $response->json("id");
            $schuler = Schuler::where("external_booking_id", $id)->first();
            if (!$schuler) {
                $schuler = new Schuler();
                $schuler->external_booking_id = $id;
            }
             $schuler->firstname = $response->json("firstname");
            $schuler->lastname = $response->json("lastname");
            $schuler->save();

            return $schuler;

        } catch (\Exception $e) {
            return null;
        }
    }
}
