<?php

namespace App\Http\Controllers\API;

use App\CloudID;
use App\Http\Controllers\Controller;
use App\Lehrer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SSOProvider extends Controller
{
    public function dozenten(Request $request)
    {
        $token = $request->input("token");
        $lehrer = Lehrer::where('securityToken', '=',$token)->first();
        if($lehrer != null)
        {
            // Login
            Auth::logout();
            // search a parent
            $obj = DB::table('model_cloud_id')->where('appName','=','klassenbuch')->where('loginId','=', $lehrer->id)->first();
            if($obj == null) {
                // try to find ..
                $find = CloudID::where('email', '=', $lehrer->email)->first();
                if ($find == null) {
                    return "Cloud-ID not found";
                } else {
                    DB::table('model_cloud_id')->insert([
                        'appName' => 'klassenbuch',
                        'loginId' => $lehrer->id,
                        'model' => 'App\Lehrer',
                        'cloud_i_d_id' => $find->id
                    ]);
                    $obj = DB::table('model_cloud_id')->where('appName','=','klassenbuch')->where('loginId','=', $lehrer->id)->first();
                }
            }
            $cloudId = CloudID::findOrFail($obj->cloud_i_d_id);
            Auth::guard('cloud')->login($cloudId);
            if (! $token = Auth::guard('api')->login($cloudId)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            Session::put("jwt_token", $token);
            Session::put("cloud_user", Auth::guard('cloud')->user());
            return redirect("/appswitcher/switch/klassenbuch");
        }
        return redirect("/login");
    }

    public function verwaltung(Request $request)
    {
        $token = $request->input("token");
        $lehrer = Lehrer::where('securityToken', '=',$token)->first();
        if($lehrer != null)
        {
            // Login
            Auth::login($lehrer);
            //die();
            return redirect("/dozent/klassenbuch");
        }
        return redirect("/login");
    }
}
