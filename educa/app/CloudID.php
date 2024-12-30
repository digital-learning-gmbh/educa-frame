<?php

namespace App;

use App\Http\Controllers\AppSwitcher;
use App\Http\Controllers\Shared\RocketChatProvider;
use App\Models\LastSeenSections;
use App\Models\Settings\CloudSetting;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use StuPla\CloudSDK\Permission\Scope;
use \StuPla\CloudSDK\Permission\Traits\HasRoles;
use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;
use TaylorNetwork\UsernameGenerator\GeneratesUsernames;
use Tymon\JWTAuth\Contracts\JWTSubject;

class CloudID extends Authenticatable implements LdapAuthenticatable
{
    use Notifiable;
    use HasRoles;
    use SoftDeletes;
    use FindSimilarUsernames;
    use AuthenticatesWithLdap;

    protected $guard = 'cloud';

    protected $fillable = [
        'name', 'email', 'password','google2fa_secret'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    protected $appends = ["has2FaKey"];

    protected $appCache = null;

    public function getDisplayNameAttribute()
    {
        return $this->name;
    }

    public function getApps()
    {
        $this->appCache = null;
        $appsRights = [];
        $appsAvaible = AppSwitcher::getAllApps();
        foreach ($appsAvaible as $app)
        {
            if($this->hasAppRights($app["appName"]))
            {
                $app["account"] = $this->appCache[$app["appName"]];
                $appsRights[] = $app;
            }
        }
        return $appsRights;
    }

    //Gibt Login-ID für übergebene App zurück, falls Rechte erteilt
    public function getAppLogin($app)
    {
        if(!$this->hasAppRights($app))
            return -1;

        return $this->appCache[$app]->loginId;
    }

    public function hasAppRights($app)
    {
        if($this->appCache == null)
            $this->buildAppCache();
        return array_key_exists($app,$this->appCache);

    }

    private function buildAppCache()
    {
        $this->appCache = [];
        $apps = DB::table('model_cloud_id')->where('cloud_i_d_id','=', $this->id)->get();
        foreach ($apps as $app)
        {
            $this->appCache[$app->appName] = $app;
        }
    }

    public function getAppEinstellung($key, $app, $default = "")
    {
        $setting = CloudSetting::where('cloud_i_d_id', '=', $this->id)->where('app','=',$app)->where('key', '=', $key)->first();
        if($setting == null)
        {
            return $default;
        }
        return $setting->value;
    }

    public function getAppEinstellungForApp($app)
    {
        return CloudSetting::where('cloud_i_d_id', '=', $this->id)->where('app','=',$app)->get();
    }

    public function setAppEinstellung($key, $app, $value)
    {
        if($value == null)
        {
            return;
        }
        $setting = CloudSetting::where('cloud_i_d_id', '=', $this->id)->where('app','=',$app)->where('key', '=', $key)->first();
        if($setting != null)
        {
            $setting->value = $value;
            $setting->save();
        } else {
            $setting = new CloudSetting();
            $setting->cloud_i_d_id = $this->id;
            $setting->app = $app;
            $setting->key = $key;
            $setting->value = $value;
            $setting->save();
        }
    }

    public function groups()
    {
        return $this->belongsToMany('App\Group', "cloudid_group", "cloudid");
    }

    public function gruppen()
    {
        return $this->groups()->get();
    }

    public function gruppen_ordered($withArchivedGroups=false)
    {
        $sql = Group::from('groups')->select('groups.*');
        if(!$withArchivedGroups)
            $sql = $sql->where("archived", "=", false);


        return $sql
            ->join('cloudid_group',function ($join)
            {
                $join->on('cloudid_group.group_id', '=','groups.id');
                $join->where('cloudid_group.cloudid','=', $this->id);
            })
            ->leftJoin('feed_activities',function ($join)
        {
            $join->on('feed_activities.belong_id','=','groups.id');
            $join->where('belong_type','=','group');
            $join->on('feed_activities.id', '=', DB::raw("(select max(stupla_feed_activities.id) from stupla_feed_activities WHERE stupla_groups.id = stupla_feed_activities.belong_id AND stupla_feed_activities.belong_type = 'group')"));

        })
            ->orderBy('feed_activities.created_at','DESC')
            ->groupBy('groups.id')
            ->get();
    }

    public function getDirectMessages()
    {
        return RocketChatProvider::getMessages($this);
    }

    public function rcUser()
    {
        return RCUser::where('cloudid','=', $this->id)->get()->first();
    }

    public function rcUserRelation()
    {
        return $this->hasOne(RCUser::class,"cloudid","id"); // RCUser::where('cloudid','=', $this->id)->get()->first();
    }

    public function invites() {
        return \Illuminate\Support\Facades\DB::table('appointment_cloud_i_d')->where([
            'cloudid' => $this->id,
            'status' => '-1'
        ]);
    }

    public function statusForAppointment($event)
    {
        $relation =  \Illuminate\Support\Facades\DB::table('appointment_cloud_i_d')->where([
            'appointment_id' => $event->id,
            'cloudid' => $this->id,
        ])->first();
        if($relation == null)
            return -1;
        return $relation->status;
    }
    public function statusForAppointmentHtml($event)
    {
        $status = $this->statusForAppointment($event);
        if($status == -1)
            return '<i class="fas fa-exclamation-circle"></i>';
        if($status == 0)
            return '<i class="far fa-question-circle"></i>';
        if($status == 1)
            return '<i class="fas fa-check"></i>';
        if($status == 2)
            return '<i class="fas fa-times"></i>';
    }


    public function administrationUser() {
        $id = $this->getAppLogin('stupla');
        if($id != null)
            return User::find($id);
        return null;
    }

    public function pushTokens()
    {
        return $this->hasMany("App\PushToken", "cloud_id");
    }

    public function getLdapDomainColumn()
    {
        return 'loginServer';
    }

    public function getLdapGuidColumn()
    {
        return 'objectguid';
    }

    public function guardName(){
        return "cloud";
    }

    public function gruppenCluster()
    {
        return $this->hasMany('App\GroupCluster',"cloudid");
    }

    public function canViewReport($report)
    {
        $roles = $this->roles;
        foreach ($roles as $role)
        {
            if(DB::table("report_role")->where([
                "role_id" => $role->id,
                "report_id" => $report->id
            ])->exists())
                return true;
        }
        return false;
    }

    public function rolesGlobal()
    {
        return $this->roles()->where('scope_name', '=', Scope::getDefaultName());
    }

    public function rolesForTenant($tenant_id)
    {
        return $this->roles()->where('scope_name', '=', Scope::getDefaultName())->where("scope_id","=",$tenant_id)->get();
    }

    public function syncRolesGlobal($ids)
    {
        try {
            DB::beginTransaction();
            //Delete old records
            $records = DB::table("model_has_roles")
                ->leftjoin("roles","model_has_roles.role_id","=","roles.id")
                ->where([
                    "model_has_roles.model_type" => "App\CloudID",
                    "model_has_roles.model_id" => $this->id,
                    'roles.scope_name' => Scope::getDefaultName()
                ])->delete();

            $data = collect();
            foreach ($ids as $id)
                $data->push(["role_id" => $id, "model_type" => "App\CloudID", "model_id" => $this->id]);
            DB::table("model_has_roles")->insert($data->toArray());
            DB::commit();
        }
        catch (\Throwable $e)
        {
            DB::rollBack();
        }

    }
    public function getSchuler()
    {
        $relation = DB::table('model_cloud_id')->where('appName','LIKE','student')->where('cloud_i_d_id','=',$this->id)->first();
        if($relation != null)
        {
            return Schuler::find($relation->loginId);
        }
        return null;
    }

    public function getLehrer()
    {
        $relation = DB::table('model_cloud_id')
            ->where('appName','LIKE','klassenbuch')
            ->where("model","=","App\Lehrer")
            ->where('cloud_i_d_id','=',$this->id)->first();
        if($relation != null)
        {
            return Lehrer::find($relation->loginId);
        }
        return null;
    }

    public function getMitarbeiter()
    {
        $relation = DB::table('model_cloud_id')->where('appName','LIKE','stupla')->where('cloud_i_d_id','=',$this->id)->first();
        if($relation != null)
        {
            return User::find($relation->loginId);
        }
        return null;
    }

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class,"tenant_cloudid","cloudid","tenant_id");
    }

    public function lastSeen()
    {
        return $this->hasMany(LastSeenSections::class,"cloud_id");
    }

    public function getHas2FaKeyAttribute()
    {
        return $this->google2fa_secret != null;
    }

    public function createDefaultGroupClusters()
    {
        $cluster = GroupCluster::where('cloudid','=',$this->id)->where("name","=","Favoriten")->first();
        if(!$cluster)
        {
            $cluster = new GroupCluster();
            $cluster->name = "Favoriten";
            $cluster->cloudid = $this->id;
            $cluster->readonly = false;
            $cluster->collapsed = false;
            $cluster->always_visible = true;
            $cluster->sort_priority = 1;
            $cluster->save();
        }
    }
}
