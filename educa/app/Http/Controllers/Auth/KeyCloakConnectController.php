<?php

namespace App\Http\Controllers\Auth;


use App\CloudID;
use App\Console\Commands\CloudIDChecker;
use App\Http\Controllers\Shared\RocketChatProvider;
use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use StuPla\CloudSDK\Permission\Models\Role;

class KeyCloakConnectController
{
    public function __construct()
    {
        $tenant = AppServiceProvider::getTenant();
        $request = request();
        $this->provider =  new \Stevenmaguire\OAuth2\Client\Provider\Keycloak([
            'authServerUrl'         => $tenant->keycloak_server,
            'realm'                 => $tenant->keycloak_realm,
            'clientId'              => $tenant->keycloak_client_id,
            'clientSecret'          => $tenant->keycloak_secret_id,
            'redirectUri'           =>  $request->getSchemeAndHttpHost().'/sso/callback',
            ]);
    }

    public function startLogin()
    {
        $authUrl = $this->provider->getAuthorizationUrl();
        Session::put('oauth2state',$this->provider->getState());
        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        $tenant = AppServiceProvider::getTenant();
        if (!$request->input("code")) {
            return redirect("/sso");
        }
        if($request->input("state") == null || $request->input("state") !== Session::get("oauth2state"))
        {
            Session::flush();
            return 'Invalid state, make sure HTTP sessions are enabled.';
        }

        try {
            $token = $this->provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);
        } catch (\Exception $e) {
            return 'Failed to get access token: '.$e->getMessage();
        }

        try {

            // We got an access token, let's now get the user's details
            $user = $this->provider->getResourceOwner($token);

            $email = $user->getEmail();

            $cloudUser = CloudID::where("email","=",$email)->first();
            if($cloudUser == null)
            {
                $cloudUser = new CloudID();
                $cloudUser->email = $email;
                $cloudUser->loginServer = $tenant->keycloak_server;
                $cloudUser->loginType = "keycloak";
                $cloudUser->password = bcrypt(Str::random(32));
                $cloudUser->name = $user->getName();
                $cloudUser->save();
                if($tenant->roleRegister != null && Role::findById($tenant->roleRegister) != null)
                    $cloudUser->roles()->sync([$tenant->roleRegister]);
            }
            $cloudUser->name = $user->getName();
            $cloudUser->save();

            Session::remove("school_id");
            Session::remove("year_id");
            Session::remove("entwurf_id");


            if (Auth::guard('cloud')->loginUsingId($cloudUser->id)) {
                CloudIDChecker::checkForSingleId(Auth::guard('cloud')->user());
                Session::put("cloud_user", Auth::guard('cloud')->user());
                $rcUser =  RocketChatProvider::login(Auth::user());

                if (! $token = Auth::guard('api')->login($cloudUser)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }

                $resp =  response(view("auth.loginKeyCloak",["failed" => false, "tenant" => $tenant,
                    "user" => Auth::user(),
                    "jwt_token" => $token,
                    "educa_rc_token" => "",
                    "educa_rc_uid" => "",
                    'expires_in' => Auth::guard('api')->factory()->getTTL() * 60 ]));

                Session::put("jwt_token", $token);
                if( $rcUser )
                {
                    $resp =  response(view("auth.loginKeyCloak",["failed" => false, "tenant" => $tenant,
                        "user" => Auth::user(),
                        "jwt_token" => $token,
                        "educa_rc_token" => $rcUser->getAuthToken(),
                        "educa_rc_uid" => $rcUser->getId(),
                        'expires_in' => Auth::guard('api')->factory()->getTTL() * 60 ]));
                    $resp->withCookie(cookie( "educa_rc_token", $rcUser->getAuthToken(), 999999999999,"/",null,false,false));
                    $resp->withCookie(cookie( "educa_rc_uid",  $rcUser->getId(), 999999999999,"/",null,false,false));
                }
                return $resp;
            }
            // Use these details to create a new profile
            return 'Failed to login';

        } catch (\Exception $e) {
            return 'Failed to get resource owner: '.$e->getMessage();
        }
    }
}
