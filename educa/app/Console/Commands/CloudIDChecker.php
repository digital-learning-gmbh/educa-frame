<?php

namespace App\Console\Commands;

use App\AdditionalInfo;
use App\Http\Controllers\Shared\RocketChatProvider;
use App\Kontakt;
use App\Lehrer;
use App\PermissionConstants;
use App\Schuler;
use App\Services\RIOSService;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CloudIDChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloud:idchecker {user?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for linked data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if($this->hasArgument("user") && $this->argument("user") != "")
        {
            $cloudID = \App\CloudID::where("email","LIKE",$this->argument("user"))->first();
            if($cloudID != null)
                CloudIDChecker::checkForSingleId($cloudID);
        } else {
            $cloudIDs = \App\CloudID::all();
            foreach ($cloudIDs as $cloudID) {
                CloudIDChecker::checkForSingleId($cloudID);
            }
        }
    }

    public static function checkForSingleId($cloudID)
    {
        // look at verwaltungsuser
        $users = User::where('email','like', $cloudID->email)->get();
        foreach ($users as $user)
        {

            if(!$cloudID->hasAppRights("stupla")) {
                DB::table('model_cloud_id')->insert([
                    'cloud_i_d_id' => $cloudID->id,
                    'model' => 'App\User',
                    'appName' => 'stupla',
                    'loginId' => $user->id
                ]);
            } else {
                DB::table('model_cloud_id')->where([
                    'cloud_i_d_id' => $cloudID->id,
                    'appName' => 'stupla',
                ])->update([
                    'cloud_i_d_id' => $cloudID->id,
                    'model' => 'App\User',
                    'appName' => 'stupla',
                    'loginId' => $user->id
                ]);
            }
        }

        // try to find teacher
        $teacher = Lehrer::where('email','=',$cloudID->email)->first();
        if($teacher != null)
        {
            DB::table('model_cloud_id')->where([
                'cloud_i_d_id' => $cloudID->id,
                'model' => 'App\Lehrer',
                'appName' => 'klassenbuch',
            ])->delete();
            DB::table('model_cloud_id')->insert([
                'cloud_i_d_id' => $cloudID->id,
                'model' => 'App\Lehrer',
                'appName' => 'klassenbuch',
                'loginId' => $teacher->id
            ]);
        }

        // try to find student
        $service = new RIOSService();
        $teilnehmer = $service->getTeilnehmer($cloudID->email);

        // Fall 1: Der Teilnehmer existiert.
        // Die Einträge werden aktualisiert oder neu erstellt.
        if($teilnehmer != null)
        {
            // Für die App 'student'
            DB::table('model_cloud_id')->updateOrInsert(
                [
                    'cloud_i_d_id' => $cloudID->id,
                    'model'      => 'App\Schuler',
                    'appName'    => 'student',
                ],
                [
                    'loginId' => $teilnehmer->id
                ]
            );

            // Für die App 'klassenbuch'
            DB::table('model_cloud_id')->updateOrInsert(
                [
                    'cloud_i_d_id' => $cloudID->id,
                    'model'      => 'App\Schuler',
                    'appName'    => 'klassenbuch',
                ],
                [
                    'loginId' => $teilnehmer->id
                ]
            );
        }
        // Fall 2: Der Teilnehmer ist null.
        // Die zugehörigen Einträge werden gelöscht.
        else
        {
            DB::table('model_cloud_id')->where([
                'cloud_i_d_id' => $cloudID->id,
                'model' => 'App\Schuler',
                'appName' => 'student',
            ])->delete();

            DB::table('model_cloud_id')->where([
                'cloud_i_d_id' => $cloudID->id,
                'model' => 'App\Schuler',
                'appName' => 'klassenbuch',
            ])->delete();
        }


        // look at dozentenzugang
        $user = Lehrer::whereRaw('LOWER(email) LIKE ?',[str_replace("ibadual.com","internationale-ba.com",$cloudID->email)])->first();
        if($user == null)
            $user = Lehrer::whereRaw('LOWER(email) LIKE ?',[str_replace("internationale-ba.com","ibadual.com",$cloudID->email)])->first();

        if ($user != null)
        {
            self::addAppIfNotHas($cloudID, "klassenbuch", $user->id, "App\Lehrer");
        }

        $addInfo = AdditionalInfo::whereRaw('LOWER(email) LIKE ?',[ str_replace("internationale-ba.com","ibadual.com",$cloudID->email)])->first();
        if($addInfo == null || Schuler::where('info_id','=',$addInfo->id)->first() == null)
            $addInfo = AdditionalInfo::whereRaw('LOWER(email) LIKE ?',[str_replace("ibadual.com","internationale-ba.com",$cloudID->email)])->first();
        if($addInfo != null && Lehrer::where('info_id','=',$addInfo->id)->first() != null)
        {
            $lehrer = Lehrer::where('info_id','=',$addInfo->id)->first();
            self::addAppIfNotHas($cloudID, "klassenbuch", $lehrer->id, "App\Lehrer");
        }

        // look at unternehmen
        $users = Kontakt::where('email','like', $cloudID->email)->get();
        foreach ($users as $user)
        {
            self::addAppIfNotHas($cloudID, "company", $user->id, "App\Kontakt");
        }

        // jeder hat Einstellungen
        self::addAppIfNotHas($cloudID, "settings", $cloudID->id, "App\CloudID");
        // Geräteplaner
        if($cloudID->hasPermissionTo("devices.open")) {
     //       self::addAppIfNotHas($cloudID, "devices", $cloudID->id, "App\CloudID");
        } else {
            self::removeAppIfNotHas($cloudID, "devices");
        }
        self::removeAppIfNotHas($cloudID, "devices");
        // Gruppen
        if($cloudID->hasPermissionTo(PermissionConstants::EDUCA_SOCIAL_OPEN)) {
            self::addAppIfNotHas($cloudID, "social", $cloudID->id, "App\CloudID");
            RocketChatProvider::syncUser($cloudID,true);
        } else {
            self::removeAppIfNotHas($cloudID, "social");
        }
        // Aufgaben
        if($cloudID->hasPermissionTo("task.open")) {
            self::addAppIfNotHas($cloudID, "tasks", $cloudID->id, "App\CloudID");
        } else {
            self::removeAppIfNotHas($cloudID, "tasks");
        }
        // Bibliothek
        if($cloudID->hasPermissionTo(PermissionConstants::EDUCA_EDU_OPEN)) {
            self::addAppIfNotHas($cloudID, "bibliothek", $cloudID->id, "App\CloudID");
        } else {
            self::removeAppIfNotHas($cloudID, "bibliothek");
        }
        // Cloud Systemsteuerung
        if($cloudID->hasPermissionTo("cloud.manage.open")) {
            // Systemsteuerung
            self::addAppIfNotHas($cloudID, "cloud", $cloudID->id, "App\CloudID");
        } else {
            self::removeAppIfNotHas($cloudID, "cloud");
        }

        if($cloudID->hasPermissionTo("home.open")) {
            // Systemsteuerung
            self::addAppIfNotHas($cloudID, "dashboard", $cloudID->id, "App\CloudID");
        } else {
            self::removeAppIfNotHas($cloudID, "dashboard");
        }

        if($cloudID->hasPermissionTo("analytics.open")) {
            // Systemsteuerung
            self::addAppIfNotHas($cloudID, "analytics", $cloudID->id, "App\CloudID");
        } else {
            self::removeAppIfNotHas($cloudID, "analytics");
        }

        // Gruppen
        if($cloudID->hasPermissionTo(PermissionConstants::EDUCA_CALENDAR_OPEN)) {
            self::addAppIfNotHas($cloudID, "calendar", $cloudID->id, "App\CloudID");
        } else {
            self::removeAppIfNotHas($cloudID, "calendar");
        }

        if($cloudID->hasPermissionTo(PermissionConstants::EDUCA_EXPLORER_OPEN)) {
            self::addAppIfNotHas($cloudID, "explore", $cloudID->id, "App\CloudID");
        } else {
            self::removeAppIfNotHas($cloudID, "explore");
        }

        if($cloudID->hasPermissionTo(PermissionConstants::EDUCA_CONTACTS_OPEN)) {
            self::addAppIfNotHas($cloudID, "contacts", $cloudID->id, "App\CloudID");
        } else {
            self::removeAppIfNotHas($cloudID, "contacts");
        }

        if($cloudID->hasPermissionTo(PermissionConstants::EDUCA_DOCUMENTS_OPEN)) {
            self::addAppIfNotHas($cloudID, "documents", $cloudID->id, "App\CloudID");
        } else {
            self::removeAppIfNotHas($cloudID, "documents");
        }

        // create default group clusters if not exist
        $cloudID->createDefaultGroupClusters();

    }

    public static function addAppIfNotHas($cloudID, $appName, $loginId, $model)
    {
        if (!$cloudID->hasAppRights($appName)) {
            DB::table('model_cloud_id')->insert([
                'cloud_i_d_id' => $cloudID->id,
                'model' => $model,
                'appName' => $appName,
                'loginId' => $loginId
            ]);
        }
    }

    public static function removeAppIfNotHas($cloudID, $appName)
    {
        if ($cloudID->hasAppRights($appName)) {
            DB::table('model_cloud_id')->where([
                'cloud_i_d_id' => $cloudID->id,
                'appName' => $appName,
            ])->delete();
        }
    }
}
