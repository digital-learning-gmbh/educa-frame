<?php

namespace App;

use App\Http\Controllers\API\ApiController;
use App\Observers\FeedObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use StuPla\CloudSDK\Permission\Models\Role;

class Section extends Model implements HasDocuments
{
    // protected $appends = ['permissions'];

    public function addSectionGroupApp($groupAppType, $parameters = "{}")
    {
        $groupApp = GroupApp::where('type', '=', $groupAppType)->first();
        if ($groupApp == null)
            return false;
        $existingApp = SectionGroupApp::where('group_app_id', '=', $groupApp->id)->where('section_id', '=', $this->id)->first();
        if ($existingApp != null) //Maximale Reiteranzahl des Typs nicht überschreiten
        {
            return false;
        }
        DB::table('section_group_apps')->insert([
            'group_app_id' => $groupApp->id,
            'section_id' => $this->id,
            'parameters' => $parameters,
            'name' => $groupApp->name
        ]);
        return true;
    }

    public function removeSectionGroupApp($sectionGroupAppId)
    {
        $sectionGroupApp = SectionGroupApp::find($sectionGroupAppId);
        if (!$sectionGroupApp)
            return false;
        return $sectionGroupApp->delete();
    }

    public function sectionGroupApps()
    {
        return $this->hasMany('App\SectionGroupApp');
    }

    public function group()
    {
        return $this->belongsTo('App\Group');
    }

    public function getGroupApp($type)
    {
        $app = GroupApp::where('type', '=', $type)->first();
        if ($app === null) {
            return null;
        } else {
            $reiter = DB::table('section_group_apps')->where('section_id', '=', $this->id)->where('group_app_id', '=', $app->id)->first();
        }
        return $reiter;
    }

    public function delete()
    {
        DB::table('appointment_section')
            ->where('section_id','=', $this->id)
            ->delete();
        DB::table('educa_wiki_pages')
            ->where('model_id','=', $this->id)
            ->where('model_type','=', 'section')
            ->delete();
        DB::table('task_section')
            ->where('section_id','=', $this->id)
            ->delete();
        DB::table('section_meetings')
            ->where('section_id','=', $this->id)
            ->delete();
        foreach ($this->roles() as $role) $role->delete();
        foreach ($this->sectionGroupApps()->get() as $sectionGroupApp) $sectionGroupApp->delete();
        return parent::delete();
    }

    public function notifiyFeed(Dokument $dokument)
    {
        $user = $dokument->creator;
        $id = ($user == null) ? "" : $user->id;
        // merge id ist group + user, d.h. es gibt dann pro Gruppe pro Nutzer max. Eintrag.
        FeedObserver::addSectionActivity($this->id, $dokument->creator, "App\CloudID", Dokument::$FEED_INFO, $this->id . "_" . $id, $dokument);

    }

    public function checkRights(Dokument $dokument, $cloudid, $type = "view"): bool
    {
        // TODO: Implement checkRights() method.
        return true;
    }

    public function getPermissionsAttribute()
    {
        if(ApiController::user() == null)
            return collect([]);
        return $this->getPermissionForCloudUser(ApiController::user()->id)
          ->pluck('name');
    }

    public function getPermissionForCloudUser($cloudUserId)
    {
        if($cloudUserId == null)
            return collect([]);
        $role = $this->group->getRole($cloudUserId);
        if(is_int($role))
            return collect([]);
        return $role
            ->map(function ($role) {
                return $role->getAllPermissions('section', $this->id);
            })->flatten();
    }

    // Hidden scoped roles
    public function roles()
    {
        return Role::where('guard_name','cloud')->where( 'scope_name','=' ,'section')->where('scope_id', $this->id)->get();
    }

    public function isAllowed($cloudUser, $permission_name)
    {
        $permissions = $this->getPermissionForCloudUser($cloudUser->id);

        foreach ($permissions as $permission)
        {
            if($permission->name == $permission_name)
                return true;
        }
        return false;
    }

    public function members()
    {
        $group = $this->group;
        $members = [];
        foreach ($group->members() as $member)
        {
            if($this->isAllowed($member, PermissionConstants::EDUCA_SECTION_VIEW))
            {
                $members[] = $member;
            }
        }
        return $members;
    }

    public function generateIndexName()
    {
        return config("educa-ai.vectorIndex.index_prefix")."section_".$this->id;
    }
}
