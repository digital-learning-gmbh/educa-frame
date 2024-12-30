<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeedActivity extends Model
{
    public function creator_display()
    {
        if($this->creator == null)
        {
            return "System";
        }
        $cloudID = CloudID::find($this->creator);
        if($cloudID == null)
        {
            return "Gelöschter Nutzer";
        }
        return $cloudID->displayName;
    }

    public function creator_role_display()
    {
        if($this->creator == null)
        {
            return collect([]);
        }
        $cloudID = CloudID::find($this->creator);
        if($cloudID == null)
        {
            return collect([]);
        }
        return $cloudID->rolesGlobal;
    }
}
