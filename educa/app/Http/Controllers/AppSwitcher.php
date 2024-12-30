<?php

namespace App\Http\Controllers;

use App\Console\Commands\CloudIDChecker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AppSwitcher extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:cloud');
    }

    public function home() {
        return parent::displayCloudView('home.main');
    }

    public function group() {
        return parent::displayCloudView('home.group');
    }

    public function cal() {
        return parent::displayCloudView('home.cal');
    }

    public function index(Request $request)
    {
        if($request->has("date"))
        {
            $selectedDay = Carbon::parse($request->input("date"));
        } else {
            $selectedDay = Carbon::today();
        }
        $firstDayOfWeek = $selectedDay->clone()->startOfWeek();
        CloudIDChecker::checkForSingleId(parent::getCloudUser());
        $user = Session::get('cloud_user');
        $apps = $user->getApps();

        if(count($apps) == 1)
        {
            return redirect('/appswitcher/switch/'.$apps[0]["appName"]);
        }
        return parent::displayCloudView('layouts.overview',["firstDayOfWeek" => $firstDayOfWeek, "selectedDay" => $selectedDay]);
    }

    public function switchApp(Request $request, $appName)
    {
        $user = Session::get('cloud_user');
        $apps = $user->getApps();
        foreach ($apps as $app)
        {
            if($app["appName"] == $appName)
            {
                $appAccount = $app["account"];
                $appUser = $appAccount->model::find($appAccount->loginId);
                if($appUser == null)
                {
                    // sehr sehr seltsam, aber kommt leider prod vor
                    return parent::displayCloudView('commons.noCloudUser');
                }
                Auth::guard($app["guard"])->login($appUser);
                return redirect($app["baseUrl"]);
            }
        }
        return redirect("/appswitcher");
    }

    public static function getAllApps()
    {
        $apps = [];

        $app = [];
        $app["name"] = "Home";
        $app["icon"] = "/images/social_launcher.png";
        $app["description"] = "Eine Startseite für educa";
        $app["appName"] = "dashboard";
        $app["guard"] = "cloud";
        $app["baseUrl"] = "home";
        $apps[] = $app;

        $app = [];
        $app["name"] = "Kommunikation";
        $app["icon"] = "/images/social_launcher.png";
        $app["description"] = "Kommuniziere mit anderen Personen in Gruppen und teile Beiträge, etc.";
        $app["appName"] = "groups";
        $app["guard"] = "cloud";
        $app["baseUrl"] = "groups";
        $apps[] = $app;

        $app = [];
        $app["name"] = "Aufgaben";
        $app["icon"] = "/images/aufgaben_launcher.png";
        $app["description"] = "Erstelle Aufgaben für Gruppen oder Benutzer und kontrolliere die Ergebnisse";
        $app["appName"] = "tasks";
        $app["guard"] = "cloud";
        $app["baseUrl"] = "tasks";
        $apps[] = $app;

        $app = [];
        $app["name"] = "Kalender";
        $app["icon"] = "/images/kalender_launcher.png";
        $app["description"] = "Im Kalendar gibt es neben dem Stundenplan, den globalen Kalender und den Kalendar aus allen Gruppen";
        $app["appName"] = "calendar";
        $app["guard"] = "cloud";
        $app["baseUrl"] = "calendar";
        $apps[] = $app;



        $app = [];
        $app["name"] = "Dokumente";
        $app["icon"] = "/images/settings_launcher.png";
        $app["description"] = "Dokumente des Nutzers";
        $app["appName"] = "documents";
        $app["guard"] = "cloud";
        $app["baseUrl"] = "documents";
        $apps[] = $app;

        $app = [];
        $app["name"] = "Bibliothek";
        $app["icon"] = "/images/edu_launcher.png";
        $app["description"] = "In der Bibliothek findest du eine große Wissenssammlung vor, um dein Wissen zu erweitern.";
        $app["appName"] = "bibliothek";
        $app["guard"] = "cloud";
        $app["baseUrl"] = "bibliothek";
        $apps[] = $app;

        $app = [];
        $app["name"] = "Geräte-Manager";
        $app["icon"] = "/images/geraete_launcher.png";
        $app["description"] = "Der Geräte-Manager dient zur Verwaltung von Leih-Geräten.";
        $app["appName"] = "devices";
        $app["guard"] = "cloud";
        $app["baseUrl"] = "devices";
        $apps[] = $app;

        $app = [];
        $app["name"] = "Kursbuch";
        $app["icon"] = "/images/klassenbuch_launcher.svg";
        $app["description"] = "Im Klassenbuch kann der Unterricht dokumentiert werden, Abwesenheiten eingetragen werden, Note eingesehen werden, etc.";
        $app["appName"] = "klassenbuch";
        $app["guard"] = "dozent";
        $app["baseUrl"] = "dozent";
        $apps[] = $app;

        $app = [];
        $app["name"] = "Verwaltung";
        $app["icon"] = "/images/stupla_launcher.png";
        $app["description"] = "In der Verwaltung kann Unterricht geplant werden, Stammdaten verwaltet werden und die Schule verwaltet werden.";
        $app["appName"] = "stupla";
        $app["guard"] = "verwaltung";
        $app["baseUrl"] = "board";
        $apps[] = $app;

//        $app = [];
//        $app["name"] = "Erkunden";
//        $app["icon"] = "/images/explore.png";
//        $app["description"] = "Erkunde die Welt von educa";
//        $app["appName"] = "explore";
//        $app["guard"] = "cloud";
//        $app["baseUrl"] = "explore";
//        $apps[] = $app;

        $app = [];
        $app["name"] = "Kontakte";
        $app["icon"] = "/images/settings_launcher.png";
        $app["description"] = "Kontakte des Nutzers";
        $app["appName"] = "contacts";
        $app["guard"] = "cloud";
        $app["baseUrl"] = "contacts";
        $apps[] = $app;

//        $app = [];
//        $app["name"] = "Analytics";
//        $app["icon"] = "/images/analytics_launcher.png";
//        $app["description"] = "";
//        $app["appName"] = "analytics";
//        $app["guard"] = "cloud";
//        $app["baseUrl"] = "analytics";
//        $apps[] = $app;

        if(config('stupla.cloud.active')) {
            $app = [];
            $app["name"] = "Systemsteuerung";
            $app["icon"] = "/images/cloud_launcher.png";
            $app["description"] = "";
            $app["appName"] = "cloud";
            $app["guard"] = "cloud";
            $app["baseUrl"] = "cloud";
            $apps[] = $app;
        }

        if(config('stupla.unternehmen.active')) {
            $app = [];
            $app["name"] = "Praxisportal";
            $app["icon"] = "/images/unternehmen_launcher.png";
            $app["description"] = "";
            $app["appName"] = "company";
            $app["guard"] = "unternehmen";
            $app["baseUrl"] = "unternehmen";
            $apps[] = $app;
        }

        $app = [];
        $app["name"] = "Einstellungen";
        $app["icon"] = "/images/settings_launcher.png";
        $app["description"] = "Allgemeine Einstellungen deines Accounts";
        $app["appName"] = "settings";
        $app["guard"] = "cloud";
        $app["baseUrl"] = "settings";
        $apps[] = $app;

        return $apps;
    }
}
