<?php

namespace App\Http\Controllers\API\V1;


use App\Http\Controllers\API\ApiController;
use App\Models\AccountRecoveryOption;
use App\Providers\AppServiceProvider;
use App\Schule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use LaravelQRCode\Facades\QRCode;
use PragmaRX\Google2FA\Google2FA;

class SettingsController extends ApiController
{
    public function getSettingsApps(Request $request)
    {
        $cloudUser = parent::getUserForToken($request);
        $settingsApps = [];

        $generalSetting = [];
        $generalSetting["name"] = "Allgemeine Einstellungen";
        $generalSetting["icon"] = "/images/settings_launcher.png";
        $generalSetting["description"] = "Allgemeine Einstellungen deines Accounts";
        $generalSetting["appName"] = "settings";
        $settingsApps[] = $generalSetting;

        $generalSetting = [];
        $generalSetting["name"] = "Sicherheit";
        $generalSetting["icon"] = "/images/protected.png";
        $generalSetting["description"] = "Sicherheitseinstellungen";
        $generalSetting["appName"] = "security";
        $settingsApps[] = $generalSetting;

        $generalSetting = [];
        $generalSetting["name"] = "Sitzungen";
        $generalSetting["icon"] = "/images/sessions.png";
        $generalSetting["description"] = "Sitzungen";
        $generalSetting["appName"] = "sessions";
        $settingsApps[] = $generalSetting;

        $generalSetting = [];
        $generalSetting["name"] = "Benachrichtigungen";
        $generalSetting["icon"] = "/images/notification.png";
        $generalSetting["description"] = "Einstellungen für Benachrichtungen";
        $generalSetting["appName"] = "notifications";
        $settingsApps[] = $generalSetting;

        return parent::createJsonResponse("",false, 200, ["settingsApps" => $settingsApps]);
    }
    public function saveGeneralSettings(Request $request)
    {
        $cloudUser = parent::getUserForToken($request);
        $cloudUser->name = $request->input("name", $cloudUser->name);
        $cloudUser->language = $request->input("language", $cloudUser->language);
        $cloudUser->save();


        return parent::createJsonResponse("",false, 200, ["cloudUser" => $cloudUser]);
    }

    public function updatePassword(Request $request)
    {
        $cloudUser = parent::getUserForToken($request);

        if ($request->has("password") && $request->input("password") != "") {
            $cloudUser->password = bcrypt($request->input("password"));
        }
        $cloudUser->save();

        return parent::createJsonResponse("",false, 200, ["cloudUser" => $cloudUser]);
    }

    public function updateProfilImage(Request $request)
    {
        $cloudUser = parent::getUserForToken($request);
        Storage::disk('public')->delete('/images/user/'.$cloudUser->image.".png");

        $file = $request->file('image');
        $image = (new ImageManager);
        $image = $image->make($file->getRealPath());
        $image = $image->fit("250");

        $name = str_random(32);
        Storage::disk('public')->put('/images/user/'.$name.".png", $image->stream('png', 90));
        $cloudUser->image = $name;
        $cloudUser->save();


        return parent::createJsonResponse("",false, 200, ["cloudUser" => $cloudUser]);
    }

    public function getSettingsForApp($appName, Request $request)
    {
        $cloudUser = parent::getUserForToken($request);
        $selectedValues = $cloudUser->getAppEinstellungForApp($appName);
        if($appName == "stupla")
        {
            return parent::createJsonResponse("",false, 200, ["options" => [ "defaultSchool" => Schule::all() ], "selectedValues" => $selectedValues]);
        }

        return parent::createJsonResponse("",false, 200, ["options" => [], "selectedValues" => [] ]);
    }

    public function saveSettingsForApp($appName, Request $request)
    {
        $cloudUser = parent::getUserForToken($request);
        $values = $request->all();
        foreach ($values as $key=>$value)
        {
            if($key != "token" && $key != "app")
            {
                $cloudUser->setAppEinstellung($key, $appName, $value);
            }
        }
        return parent::createJsonResponse("",false, 200);
    }

    public function twoFAToggle( Request $request)
    {
        $cloudUser = parent::getUserForToken($request);
        if($cloudUser->google2fa_secret == null) {
            $google2fa = new Google2FA();
            $cloudUser->google2fa_secret = $google2fa->generateSecretKey(64);
            $cloudUser->save();
        } else {
            $cloudUser->google2fa_secret = null;
            $cloudUser->save();
        }
        return parent::createJsonResponse("",false, 200, ["cloudUser" => $cloudUser]);
    }

    public function twoFAqrCode(Request $request)
    {
        $cloudUser = parent::getUserForToken($request);

        $tenant = AppServiceProvider::getTenant();
        $google2fa = new Google2FA();
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            $tenant->name,
            $cloudUser->email,
            $cloudUser->google2fa_secret
        );
        return QRCode::text($qrCodeUrl)
            ->svg();
    }

    public function security(Request $request)
    {
        $cloudUser = parent::getUserForToken($request);
        $securitySettings = AccountRecoveryOption::where("cloud_id","=",$cloudUser->id)->first();
        if($securitySettings == null)
        {
            $securitySettings = new AccountRecoveryOption();
            $securitySettings->cloud_id = $cloudUser->id;
            $securitySettings->save();
        }
        return parent::createJsonResponse("",false, 200, ["securitySettings" => $securitySettings]);
    }

    public function saveSecurity(Request $request)
    {
        $cloudUser = parent::getUserForToken($request);
        $securitySettings = AccountRecoveryOption::where("cloud_id","=",$cloudUser->id)->first();
        if($securitySettings == null)
        {
            $securitySettings = new AccountRecoveryOption();
            $securitySettings->cloud_id = $cloudUser->id;
            $securitySettings->save();
        }
        $securitySettings->emailRecover = $request->input("emailRecover");
        $securitySettings->questionRecover = $request->input("questionRecover");
        $securitySettings->secondEmailRecover = $request->input("secondEmailRecover");
        $securitySettings->firstQuestion = $request->input("firstQuestion");
        $securitySettings->firstAnswer = $request->input("firstAnswer");
        $securitySettings->secondQuestion = $request->input("secondQuestion");
        $securitySettings->secondAnswer = $request->input("secondAnswer");
        $securitySettings->secondEmail = $request->input("secondEmail");
        $securitySettings->save();

        return parent::createJsonResponse("",false, 200, ["securitySettings" => $securitySettings]);
    }
}
