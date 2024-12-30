<?php

namespace App\Http\Middleware;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\StatusCodes;
use App\Models\SessionToken;
use App\Token;
use Carbon\Carbon;
use Closure;
use Httpful\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\ActivitylogServiceProvider;

class ApiAuth
{
    protected $except = [
        'api/v1/documents/*',
        'api/v1/h5p/*',
        'api/v1/formtemplates/open',
        'api/v1/privacy'
    ];
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next)
    {
        // Get token via http params / body
        if($this->inExceptArray($request))
        {
            return $next($request);
        }


        $tokenString = trim($request->bearerToken() ? $request->bearerToken() : $request->input("token"));
        $token = SessionToken::where("token","LIKE",$tokenString)->first();
        if($token == null)
        {
            return ApiController::createJsonResponseStatic("Token is invalid", true,499, null, StatusCodes::ERROR_TOKEN_INVALID);
        }
        $token->last_seen = Carbon::now();
        $token->save();

        $user = $token->user;

        if($user != null)
        {
            try {
                config(['activitylog.default_auth_driver' => 'api']);
                (new ActivitylogServiceProvider(app()))->register();
            } catch (\Exception $exception)
            {
                //
            }
            return $next($request);
        }
        return ApiController::createJsonResponseStatic("Token is invalid", true,499, null, StatusCodes::ERROR_TOKEN_INVALID);

    }

    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }

}
