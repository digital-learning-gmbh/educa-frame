<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelQRCode\Facades\QRCode;
use App\Http\Controllers\API\ApiController;
use App\Providers\AppServiceProvider;

class QrCodeController extends ApiController
{
    public function generate(Request $request)
    {
        $tenant = AppServiceProvider::getTenant();
        unset($tenant->impressum);

        $chat = str_replace("https://","",config('laravel-rocket-chat.instance'));
        return QRCode::text(json_encode([
            "jwt" => $request->input("token"),
            "cloud_user" => Auth::guard('api')->user(),
            "server" => request()->getSchemeAndHttpHost(),
            "tenant" => $tenant,
            "chatserver" => $chat
            ]))
            ->svg();
    }
}
