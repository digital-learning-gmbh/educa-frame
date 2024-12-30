<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Shared\RocketChatProvider;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Mexitek\PHPColors\Color;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function displayCloudView($view, $params = null) {
        $cloud_user = $this->getCloudUser();
        if($cloud_user == null) {
            Session::flush();
            Auth::logout();
            return redirect('/login');
        }
        $addParams = ['cloud_user' => $cloud_user, "current_rcUser" => null ];
        if($params == null)
        {
            return view($view, $addParams );
        } else {
            return view($view, array_merge ($addParams , $params ));
        }
    }

    public static function getCloudUser() {
        return Session::get('cloud_user');
    }

    public function loadCss()
    {
        $tenant = AppServiceProvider::getTenant();
        $color = new Color($tenant->color);
        // $color->darken(10);
        return str_replace("#227dc7",$color->darken(10),str_replace("#3490dc",$tenant->color,file_get_contents(public_path("css/app.css"))));
    }
}
