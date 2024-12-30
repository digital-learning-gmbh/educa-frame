<?php

namespace App\Http\Controllers\API;

use App\APIKey;
use App\Http\Controllers\Controller;
use App\Models\SessionToken;
use App\Token;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *      version="0.0.1",
 *      title="educa API",
 *      description="educa API description",
 *      @OA\Contact(
 *         email="kontakt@schule-plus.com"
 *      )
 * )
 *
 */



class ApiController extends Controller
{
    //

    private static $user;

    public static function setUserManual($user)
    {
        self::$user = $user;
    }
    /**
     * Checks whether the user has the group
     *
     * @param $clouduser
     * @param $group_id
     * @return bool
     */
    protected function isCloudUserInGroup($clouduser, $group_id): bool
    {
        $grps = $clouduser->gruppen();
        if(!$grps)
            return false;
        foreach($grps as $g )
        {
            if($g->id == $group_id)
                return true;
        }
        return false;
    }

    /**
     * Checks whether the section is in a group and a group belongs to a user
     *
     * @param $clouduser
     * @param $section_id
     * @return bool
     */
    protected function isSectionInGroupOfCloudUser($clouduser, $section_id): bool
    {
        $grps = $clouduser->gruppen();
        if(!$grps)
            return false;
        foreach($grps as $g )
        {
            $sections = $g->sections()->get();
            if(  $sections )
                foreach ( $sections as $s)
                    if( $s->id == $section_id)
                        return true;
        }
        return false;
    }

    protected static function checkSecurityToken(Request $request)
    {
        $token = APIKey::where('token','=',$request->input("security_token"))->first();
        if($token == null)
        {
            die("Invalid security token '".$request->input("security_token")."'. Attempt will be recorded");
        }
    }

    public static function getUserForToken(Request $request)
    {
        $tokenString = trim($request->bearerToken() ? $request->bearerToken() : ($request->input("token") ? $request->input("token") : $request->cookie("token")));
        $sessionToken = SessionToken::where("token","LIKE",$tokenString)->first();
        if($sessionToken == null)
            return null;
        return $sessionToken->user;
    }

    public static function createJsonResponseStatic($message, $is_error, $http_status_code, $payload = null, $custom_status_code = null, $forceEncryption = false): Response
    {
        $pld =
            [
                "status" => isset($custom_status_code)? $custom_status_code : ($is_error? -1 : 1),
                "message" => $message,
            ];
        if($payload) {
            $pld["payload"] = $payload;
            if((config("educa.encrypt") || $forceEncryption )&& !(defined('PHPUNIT_YOURAPPLICATION_TESTSUITE') && PHPUNIT_YOURAPPLICATION_TESTSUITE)) {
                $theOtherKey    = "67ef74t5YPdbf8au"; //32 character long
                $text           = json_encode($pld["payload"]); //or the text that you want to encrypt.
                $newEncrypter   = new \Illuminate\Encryption\Encrypter ($theOtherKey,'AES-128-CBC');
                $pld["payload"]      = $newEncrypter->encrypt($text,false);
                $pld["encrypt"] = true;
            } else {
                $pld["encrypt"] = false;
            }
        }
        return new Response($pld, $http_status_code);
    }

    public static function user()
    {
        if(self::$user != null)
            return self::$user;
        $sessionToken = self::sessionToken();
        if($sessionToken == null)
            return null;
        return $sessionToken->user;
    }

    public static function sessionToken()
    {
        $tokenString = trim(\request()->bearerToken() ? \request()->bearerToken() : \request()->input("token"));
        return SessionToken::where("token","LIKE",$tokenString)->first();
    }

    /**
     * Gibt eine HTTP response zurück
     * @param $message
     * @param $is_error
     * @param $http_status_code
     * @param null $payload
     * @return Response
     */
    protected function createJsonResponse($message, $is_error, $http_status_code, $payload = null, $custom_status_code=null, $forceEncryption = false): Response
    {
        return $this::createJsonResponseStatic($message, $is_error, $http_status_code, $payload, $custom_status_code, $forceEncryption);
    }
}
