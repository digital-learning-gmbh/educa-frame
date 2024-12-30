<?php

namespace App\Http\Controllers\Auth;

use App\AdditionalInfo;
use App\CloudID;
use App\Console\Commands\CloudIDChecker;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\RocketChatProvider;
use App\Lehrer;
use App\Models\SessionToken;
use App\Providers\AppServiceProvider;
use App\Schuler;
use App\SystemEinstellung;
use App\Token;
use Carbon\Carbon;
use Dcblogdev\MsGraph\Facades\MsGraph;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FA\Google2FA;
use StuPla\CloudSDK\Permission\Models\Role;

class ReactLoginController extends Controller
{

    use AuthenticatesUsers;
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(\Illuminate\Http\Request $request)
    {

        Session::remove("school_id");
        Session::remove("year_id");
        Session::remove("entwurf_id");

        $credentials = [
            'email' => $request->input("email"),
            'password' => $request->input("password")
        ];

        if (Auth::guard('cloud')->attempt($credentials)) {
            CloudIDChecker::checkForSingleId(Auth::user());

            // check if 2fa auth required
            $cloud = Auth::user();
            if($cloud->google2fa_secret != null)
            {
                if(!$request->has("2fa") || $request->input("2fa") == null)
                {
                    return ApiController::createJsonResponseStatic("2fa required, please send again with 2fa", true, 401, ["2fa_required" => true, "2fa" => false, "password" => true]);
                }

                $digits = $request->input("2fa");
                $google2fa = new Google2FA();
                $valid = $google2fa->verifyKey($cloud->google2fa_secret, $digits);
                if(!$valid)
                {
                    return ApiController::createJsonResponseStatic("2fa code was wrong", true, 401,["2fa_required" => true, "2fa" => false, "password" => true]);
                }
            }

            Session::put("cloud_user", Auth::user());
            $rcUser =  null; //RocketChatProvider::login(Auth::user());
            $request->session()->regenerate();

            $this->clearLoginAttempts($request);

            $token = str_random(128);

            $resp =  ApiController::createJsonResponseStatic("Login war erfolgreich",false, 200,
                ["user" => Auth::user(),
                    "token" => $token ]);
            //$resp->withCookie(cookie( "token", $token, 999999999999,"/",null,false,false));

            $tokenString = trim($token);
            $token = SessionToken::where("token","LIKE",$tokenString)->first();
            if($token != null)
            {
                throw new \Exception("duplicate session token!");
            }
            Session::put("jwt_token", $token);
            $token = new SessionToken();
            $token->token = $tokenString;
            $token->cloudid = $cloud->id;
            $token->last_seen = Carbon::now();
            $token->browser = $request->input("browser");
            $token->app = $request->input("app");
            $token->device = $request->input("device");
            $token->os = $request->input("os");
            $token->save();


            if( $rcUser )
            {
                $resp->withCookie(cookie( "educa_rc_token", $rcUser->getAuthToken(), 999999999999,"/",null,false,false));
                $resp->withCookie(cookie( "educa_rc_uid",  $rcUser->getId(), 999999999999,"/",null,false,false));
            }
            return $resp;
        }

        return ApiController::createJsonResponseStatic("Benutzername oder Passwort ist falsch",true, 401);
    }


    public function logout(\Illuminate\Http\Request $request)
    {
        $tokenString = trim($request->bearerToken() ? $request->bearerToken() : $request->input("token"));
        $token = SessionToken::where("token","LIKE",$tokenString)->first();
        if($token != null)
        {
            $token->delete();
        }

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        Session::flush();
        Session::start();
        return ApiController::createJsonResponseStatic("Logout war erfolgreich. Goodbye!",false, 200, []);
    }

    public function refresh(\Illuminate\Http\Request $request)
    {
        $nuToken =auth('api')->refresh();
        Session::put("jwt_token", $nuToken);
        return ApiController::createJsonResponseStatic("token refreshed",false, 200,
            ["token" => $nuToken]);
    }

    public function derive(\Illuminate\Http\Request $request)
    {
        $tokenString = trim($request->bearerToken() ? $request->bearerToken() : $request->input("token"));
        $token = SessionToken::where("token","LIKE",$tokenString)->first();
        $user = $token->user;
        if(!$user)
            return ApiController::createJsonResponseStatic("token invalid.",true, 400);

        $token = str_random(128);

        $tokenString = trim($token);
        $token = SessionToken::where("token","LIKE",$tokenString)->first();
        if($token != null)
        {
            throw new \Exception("duplicate session token!");
        }
        $tokenSession = new SessionToken();
        $tokenSession->token = $tokenString;
        $tokenSession->cloudid = $user->id;
        $tokenSession->last_seen = Carbon::now();
        $tokenSession->browser = $request->input("browser");
        $tokenSession->app = $request->input("app");
        $tokenSession->device = $request->input("device");
        $tokenSession->os = $request->input("os");
        $tokenSession->save();

        $rcUser =  RocketChatProvider::login($user);

        $resp = ApiController::createJsonResponseStatic("derivation ok.",false, 200,
            [   "user" => $user,
                "token" => $tokenString
            ]);
        if( $rcUser )
        {
            $resp->withCookie(cookie( "educa_rc_token", $rcUser->getAuthToken(), 999999999999,"/",null,false,false));
            $resp->withCookie(cookie( "educa_rc_uid",  $rcUser->getId(), 999999999999,"/",null,false,false));
        }
        return $resp;
    }

    public function registerReact(Request $request)
    {
        $name = $request->input("name");
        $pass = $request->input("password");
        $email = $request->input("email");

        if(CloudID::where("email","=",$email)->exists())
        {
            return ApiController::createJsonResponseStatic("Benutzer existiert bereits",true, 401,["errorCode" => 1]);
        }

        $cloud = new CloudID();
        $cloud->name = $name;
        $cloud->password = bcrypt($pass);
        $cloud->email = $email;
        $cloud->save();

        $tenant = AppServiceProvider::getTenant();
        if($tenant != null && $tenant->roleRegister != null && Role::where("id","=",$tenant->roleRegister)->where("scope_id","=",$tenant->id)->first() != null)
        {
            $cloud->roles()->sync([Role::where("id","=",$tenant->roleRegister)->where("scope_id","=",$tenant->id)->first()?->id]);
        }

        return $this->login($request);
    }
}
