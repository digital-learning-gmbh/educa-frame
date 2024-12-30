<?php

namespace App\Http\Controllers\API\V1\Groups\GroupTemplates;

use App\Models\GroupTemplate;

class GroupDatabaseTemplate implements AbstractGroupTemplate
{
    private $groupTemplate;

    public function __construct(GroupTemplate $groupTemplate)
    {
        $this->groupTemplate = $groupTemplate;
    }

    public function name($name)
    {
        if($name == null)
            return $this->groupTemplate->name;
        return $name;
    }

    public function startImage()
    {
        return $this->groupTemplate->image;
    }

    public function roles()
    {
        $roles = json_decode($this->groupTemplate->roles,true);
        if($roles == null)
            return [];
        return $roles;
    }

    public function topics()
    {
        $topics = json_decode($this->groupTemplate->topics,true);
        if($topics == null)
            return [];
        return $topics;
    }

    public function color()
    {
        return $this->groupTemplate->color;
    }
}
