<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use StuPla\CloudSDK\Permission\Models\Role;

class AdressbookEntry extends Model
{
    use HasFactory;

    public function addRoles($roles)
    {
        foreach ($roles as $role)
        {
            DB::table("adressbook_entry_role")
                ->updateOrInsert(
                    [
                        "role_id" => $role->id,
                        "adressbook_entry_id" => $this->id
                    ]);
        }
    }

    public function roleIds()
    {
        return DB::table("adressbook_entry_role")->where("adressbook_entry_id" ,"=", $this->id)->pluck("role_id");
    }
}
