<?php

namespace App\Http\Controllers\Auth;

use App\CloudID;
use App\Console\Commands\CloudIDChecker;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\RocketChatProvider;
use App\Models\SessionToken;
use App\Providers\AppServiceProvider;
use Carbon\Carbon;
use Dcblogdev\MsGraph\Facades\MsGraph;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MSCallbackHandler extends Controller
{
    public function oauth(Request $request)
    {
        $tenant = AppServiceProvider::getTenant();

        // override config
        config(['msgraph.clientId' => $tenant->ms_graph_client_id]);
        config(['msgraph.clientSecret' => $tenant->ms_graph_secret_id]);
        config(['msgraph.urlAuthorize' => 'https://login.microsoftonline.com/'.$tenant->ms_graph_tenant_id.'/oauth2/v2.0/authorize']);
        config(['msgraph.urlAccessToken' => 'https://login.microsoftonline.com/'.$tenant->ms_graph_tenant_id.'/oauth2/v2.0/token']);
        config(['msgraph.redirectUri' => 'https://'.$tenant->domain.'/msgraph/oauth']);
config(['msgraph.msgraphLandingUri' => 'https://'.$tenant->domain.'/msgraph/oauth']);

        try {
            $msGraph = MsGraph::get('me');
        } catch (\Exception $exception)
        {
            return redirect("/");
        }

        // create a jwt token to login
        $email = $msGraph["userPrincipalName"];
        $cloudId = CloudID::where("email","=",$email)->first();
        if($cloudId == null)
            return view("auth.loginOAuth",["msGraph" => $msGraph, "failed" => true]);

        Session::remove("school_id");
        Session::remove("year_id");
        Session::remove("entwurf_id");


        if (Auth::guard('cloud')->loginUsingId($cloudId->id)) {
            CloudIDChecker::checkForSingleId(Auth::guard('cloud')->user());
            Session::put("cloud_user", Auth::guard('cloud')->user());
            $rcUser =  RocketChatProvider::login(Auth::user());

            if (! $token = Auth::guard('api')->login($cloudId)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $tokenString = trim($token);
            $token = SessionToken::where("token","LIKE",$tokenString)->first();
            if($token != null)
            {
                throw new \Exception("duplicate session token!");
            }
            Session::put("jwt_token", $token);
            $token = new SessionToken();
            $token->token = $tokenString;
            $token->cloudid = $cloudId->id;
            $token->last_seen = Carbon::now();
            $token->browser = $request->input("browser");
            $token->app = $request->input("app");
            $token->device = $request->input("device");
            $token->os = $request->input("os");
            $token->save();

            $resp =  response(view("auth.loginOAuth",["msGraph" => $msGraph, "failed" => false,
                "user" => Auth::user(),
                "jwt_token" => $tokenString,
                "educa_rc_token" => "",
                "educa_rc_uid" => "",
                'expires_in' => Auth::guard('api')->factory()->getTTL() * 60 ]));

            Session::put("jwt_token", $tokenString);
            if( $rcUser )
            {
                $resp =  response(view("auth.loginOAuth",["msGraph" => $msGraph, "failed" => false,
                    "user" => Auth::user(),
                    "jwt_token" => $tokenString,
                    "educa_rc_token" => $rcUser->getAuthToken(),
                    "educa_rc_uid" => $rcUser->getId(),
                    'expires_in' => Auth::guard('api')->factory()->getTTL() * 60 ]));
                $resp->withCookie(cookie( "educa_rc_token", $rcUser->getAuthToken(), 999999999999,"/",null,false,false));
                $resp->withCookie(cookie( "educa_rc_uid",  $rcUser->getId(), 999999999999,"/",null,false,false));
            }
            return $resp;
        }


        return view("auth.loginOAuth",["msGraph" => $msGraph, "failed" => true]);
    }

    public function oauthMobile(Request $request)
    {
        $tenant = AppServiceProvider::getTenant();

        // override config
        config(['msgraph.clientId' => $tenant->ms_graph_client_id]);
        config(['msgraph.clientSecret' => $tenant->ms_graph_secret_id]);
        config(['msgraph.urlAuthorize' => 'https://login.microsoftonline.com/'.$tenant->ms_graph_tenant_id.'/oauth2/v2.0/authorize']);
        config(['msgraph.urlAccessToken' => 'https://login.microsoftonline.com/'.$tenant->ms_graph_tenant_id.'/oauth2/v2.0/token']);
        config(["msgraph.redirectUri" => $request->getSchemeAndHttpHost()."/msgraph/oauthMobile",
            "msgraph.msgraphLandingUri" => $request->getSchemeAndHttpHost()."/msgraph/oauthMobile"
        ]);
        try {
            $msGraph = MsGraph::get('me');
        } catch (\Exception $exception)
        {
            return redirect("/");
        }


        // create a jwt token to login
        $email = $msGraph["mail"];
        $cloudId = CloudID::where("email","=",$email)->first();
        if($cloudId == null)
            return ApiController::createJsonResponseStatic("error",true,400,["message" => "User is not allowed to login via Office 365"]);

        Session::remove("school_id");
        Session::remove("year_id");
        Session::remove("entwurf_id");


        if (Auth::guard('cloud')->loginUsingId($cloudId->id)) {
            CloudIDChecker::checkForSingleId(Auth::guard('cloud')->user());
            Session::put("cloud_user", Auth::guard('cloud')->user());
            $rcUser =  RocketChatProvider::login(Auth::user());
            $request->session()->regenerate();

            if (! $token = Auth::guard('api')->login($cloudId)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            Session::put("jwt_token", $token);
            if( $rcUser )
            {
                return ApiController::createJsonResponseStatic("success",false,200,["msGraph" => $msGraph, "failed" => false,
                    "user" => Auth::user(),
                    "jwt_token" => $token,
                    "educa_rc_token" => $rcUser->getAuthToken(),
                    "educa_rc_uid" => $rcUser->getId(),
                    'expires_in' => Auth::guard('api')->factory()->getTTL() * 60 ]);

            }

            return ApiController::createJsonResponseStatic("success",false,200,["msGraph" => $msGraph, "failed" => false,
                "user" => Auth::user(),
                "jwt_token" => $token,
                "educa_rc_token" => "",
                "educa_rc_uid" => "",
                'expires_in' => Auth::guard('api')->factory()->getTTL() * 60 ]);
        }


        return ApiController::createJsonResponseStatic("error",true,400,["message" => "login was not successful"]);
    }
}
