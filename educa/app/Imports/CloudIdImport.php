<?php
namespace App\Imports;
use App\CloudID;
use App\Models\Tenant;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use StuPla\CloudSDK\Permission\Models\Role;

class CloudIdImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        if(CloudID::where("email","=",$row["email"])->exists())
        {
            return null;
        }

        $cloudId = new CloudID();
        $cloudId->name = $row["vorname"]." ".$row["nachname"];
        $cloudId->email = $row["email"];
        $cloudId->password  = bcrypt($row["passwort"]);
        if(array_key_exists("datenschutz_akzeptiert",$row)) {
            $cloudId->agreedPrivacy = (strtolower($row["datenschutz_akzeptiert"]) == "ja");
        }
        $cloudId->loginServer = 'local';
        $cloudId->loginType = 'eloquent';
        $cloudId->save();

        foreach(explode(",", $row["rollen"]) as $role)
            $cloudId->roles()->attach(Role::where('name',"=", trim($role))->first());

        foreach(explode(",", $row["tenants"]) as $tenant)
            $cloudId->tenants()->attach(Tenant::where('name',"=", trim($tenant))->first());

        return $cloudId;
    }
}
