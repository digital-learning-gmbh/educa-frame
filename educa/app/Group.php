<?php

namespace App;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\AppController;
use App\Models\ExternalIntegration;
use App\Models\GroupCache;
use App\Models\Tenant;
use App\Observers\FeedObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use StuPla\CloudSDK\Permission\Models\Role;

class Group extends Model implements HasMeetings
{
    protected $appends = ['tenant'];


    public function mostRecent()
    {
        //letzten Beitrag darstellen
        $mostRecent = Array();
        $beitraege = $this->reiters(1)[0]; //filter nach typ beiträge, ersten nehmen

        if(count($beitraege->beitrags()) == 0)
        {
            $mostRecent["short"] = "Noch keine Beiträge vorhanden";
            $mostRecent["time"] = "";
            $mostRecent["author"] = "";
        }
        else
        {
            $beitrag = $beitraege->beitrags()->last();
            $mostRecent["short"] = Str::limit($beitrag->content, 250, $end="...");
            $time = new Carbon(new \DateTime($beitrag->created_at));
            $mostRecent["time"] = $time->diffForHumans();
            $mostRecent["author"] = CloudID::findOrFail($beitrag->cloudid)->displayName;
        }
        return $mostRecent;
    }

    public function removeReiter($reiterId)
    {
        $reiterToRemove = $this->reiters()[$reiterId];
        if($reiterToRemove->can_delete)
        {
            $reiterToRemove->remove();
            return DB::table('gruppe_reiter')->where("id", "=", $reiterToRemove->id)->delete();
        }
        else
        {
            return false;
        }
    }

    public function members($rcUsernames = false)
    {
        $cloudIds = DB::table('cloudid_group')->where('group_id','=', $this->id)->pluck('cloudid');
        if(!$rcUsernames)
            return CloudID::find($cloudIds);
        return DB::table('rc_users')->whereIn('cloudid', $cloudIds)->pluck('username');
    }

    public function membersWithRoles()
    {
        $members = array();
        $rel = DB::table('cloudid_group')->where('group_id','=', $this->id)->get();
        foreach($rel as $relation)
        {
            $memberModel = CloudID::find($relation->cloudid);
            if($memberModel == null)
            {
                DB::table("cloudid_group")->where('group_id','=', $this->id)->where("cloudid","=",$relation->cloudid)->delete();
                continue;
            }
            $members[] = [
                "id" => $relation->cloudid,
                "name" => $memberModel->displayName,
                "email" => $memberModel->email,
                "role" => $memberModel->roles()->where('scope_name','group')->where('scope_id', $this->id)->get()
            ];
        }
        return $members;
    }

    public function isMember($cloudid)
    {
        return DB::table('cloudid_group')->where('group_id','=', $this->id)->where("cloudid", "=", $cloudid)->exists();
    }

    public function getRole($cloudid)
    {
        if(!$this->isMember($cloudid))
            return -1;
        return CloudID::find($cloudid)->roles()->where('scope_name','group')->where('scope_id', $this->id)->get();
    }

    public function addMember($cloudid)
    {
        if(!$this->isMember($cloudid)){
            DB::table('cloudid_group')->insert([
                'group_id' => $this->id,
                'cloudid' => $cloudid,
            ]);
        }
    }

    public function removeMember($cloudid)
    {
        return DB::table('cloudid_group')->where('group_id','=', $this->id)->where("cloudid", "=", $cloudid)->delete();
    }

    public function dokumente($parent_id = null)
    {
        $ids = DB::table('model_dokument')
            ->where('model_id', '=', $this->id)
            ->where('model_type', '=', 'group')
            ->pluck('dokument_id')->toArray();
        if($parent_id == null)
        {
            return Dokument::find($ids);
        }
        return Dokument::where('parent_id', '=', '0')->whereIn('id', $ids)->get();
    }

    public function aufgabe()
    {
        return $this->belongsToMany('App\Hausaufgabe','hausaufgabe_gruppe');
    }

    public function setName($name)
    {
        $this->name = $name;
        $this->save();
    }

    public function setColor($color)
    {
        $this->color = $color;
        $this->save();
    }

    public function setType($type)
    {
        $this->type = $type;
        $this->save();
    }

    public function setDescription($description)
    {
        $this->description = $description;
        $this->save();
    }

    public function getTenantAttribute()
    {
        if($this->tenant_id == null)
            return null;
        return Tenant::where("id","=",$this->tenant_id)->first();
    }

    public function getMembersCountAttribute()
    {
       return count($this->members());
    }

    public function addSection($name = "Neuer Bereich")
    {
        $section = new Section();
        $section->group_id = $this->id;
        $section->name = $name;
        $section->save();

        // override the default settings
        foreach ($this->roles() as $role) {
            $subRole = \StuPla\CloudSDK\Permission\Models\Role::create(['guard_name' => 'cloud', 'name' => $role->id, 'scope_name' => 'section', 'scope_id' => $section->id]);

            foreach ($role->permissions as $permission) {
                if($permission->scope_name == "section") {
                    $subRole->givePermissionTo($permission);
                }
            }
            $role->assignRole($subRole);
        }
        $lowestOrder = Section::where("group_id","=",$this->id)->min("order");
        $section->order = $lowestOrder-1;
        $section->save();

        GroupCache::clearGroup($this->id);

        return $section;
    }

    public function sections()
    {
        return $this->hasMany('App\Section');
    }

    public function delete()
    {
        DB::table("cloudid_group")->where('group_id','=', $this->id)->delete();
        $code = AccessCode::where('model_id','=', $this->id)->where("type","=","group")->delete();
        foreach($this->dokumente() as $document) $document->delete();
        foreach($this->sections as $section) $section->delete();

        GroupCache::clearGroup($this->id);
        return parent::delete();
    }

    public function notifiyFeed(Dokument $dokument)
    {
        $user = $dokument->creator;
        $id = ($user == null) ? "" : $user->id;
        // merge id ist group + user, d.h. es gibt dann pro Gruppe pro Nutzer max. Eintrag.
        FeedObserver::addGroupActivity($this->id, $dokument->creator,"App\CloudID",Dokument::$FEED_INFO,$this->id."_".$id, $dokument);
    }

    public function isArchived()
    {
        return $this->archived;
    }

    public function setArchived($flag)
    {
        $this->archived = $flag;
        $this->save();

        GroupCache::clearGroup($this->id);
    }

    public function createRolesTemplate()
    {
        $roles = Role::where('scope_name', 'group')->where('scope_id','template')->get();
        foreach ($roles as $role)
        {
            Log::info("Create role ". $role->name . " from template for group ". $this->name. " (ID #".$this->id.")");
            // create an instance role
            $instanceRole = \StuPla\CloudSDK\Permission\Models\Role::create(['guard_name' => 'cloud', 'name' => $role->name,'scope_name' => 'group', 'scope_id' => $this->id]);
            foreach ($role->permissions as $permission)
            {
                Log::info("Role ". $instanceRole->name . " add permission ". $permission->name . " (ID #".$permission->id.")");
                $instanceRole->givePermissionTo($permission);
            }
        }
    }

    public function getRoleForName($name)
    {
        return Role::findByName($name, 'cloud', 'group', $this->id);
    }

    public function roles()
    {
        return Role::where('guard_name','cloud')->where( 'scope_name', 'group')->where('scope_id', $this->id)->get();
    }

    public function getRolesAttribute()
    {
        return $this->roles();
    }

    public function getPermissionsAttribute()
    {
        if(ApiController::user() == null)
            return collect([]);
       return ApiController::user()->getAllPermissions('group',$this->id)->pluck('name');
    }

    public function isAllowed($cloudUser, $permission_name)
    {
        $permissions = $cloudUser->getAllPermissions('group',$this->id);
        foreach ($permissions as $permission)
        {
            if($permission->name = $permission_name)
                return true;
        }
        return false;
    }

    public function getRolesWithPermissionAttribute()
    {
        $roles = [];
        // alle gruppen rollen
        foreach ($this->roles() as $role)
        {
            $objRole = [];
            $objRole["role"] = $role;
            $objRole["groupPermissions"] = $role->getAllPermissions('group', $this->id)->pluck('name');
            $objRole["sections"] = [];
            foreach ($this->sections as $section) {
                $sectionRights["section"] = $section;
                $sectionRights["sectionPermissions"] = $role->getAllPermissions('section', $section->id)->pluck('name');
                $objRole["sections"][] = $sectionRights;
            }
            $roles[] = $objRole;
        }
        return $roles;
    }

    public function getMemberRole()
    {
        return $this->getRoleForName("Mitglied");
    }

    public function getAdminRole()
    {
        return $this->getRoleForName("Besitzer");
    }


    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
    public function checkMeetingRights(CloudID $user): bool
    {
        return $this->isAllowed($user,PermissionConstants::EDUCA_GROUP_MEETING_CREATE);
    }

    public function name()
    {
        return $this->name;
    }

    public function externalIntegrations()
    {
        return $this->hasMany(ExternalIntegration::class);
    }

    public function welcomeText()
    {
        return "";
    }
}
