<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppReactController extends AppController
{
    public function showReactApp()
    {
        return parent::displayUserView('react_admin');
    }
}
