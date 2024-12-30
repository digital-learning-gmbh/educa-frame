<?php

namespace App\Http\Controllers\API\V1\Groups\GroupTemplates;

interface AbstractGroupTemplate
{

    public function name($name);

    public function startImage();

    public function roles();

    public function topics();

    public function color();
}
