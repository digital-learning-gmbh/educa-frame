<?php

namespace App\Models;

use App\CloudID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedCardRead extends Model
{
    use HasFactory;

    public function creator_display()
    {
        if($this->creator == null)
        {
            return "System";
        }
        $cloudID = CloudID::find($this->cloud_i_d_id);
        if($cloudID == null)
        {
            return "Gelöschter Nutzer";
        }
        return $cloudID->displayName;
    }

    public function creator_role_display()
    {
        if($this->cloud_i_d_id == null)
        {
            return collect([]);
        }
        $cloudID = CloudID::find($this->cloud_i_d_id);
        if($cloudID == null)
        {
            return collect([]);
        }
        return $cloudID->rolesGlobal;
    }
}
