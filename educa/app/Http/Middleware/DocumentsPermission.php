<?php

namespace App\Http\Middleware;

use App\Dokument;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\StatusCodes;
use App\Models\SessionToken;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class DocumentsPermission extends ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get token via http params / body
        if(/*$request->has("document_id") &&*/ $request->has("access_hash"))
        {
            $hash = $request->access_hash;
            $document = Dokument::where(["access_hash"=>$hash])->first();
            if(!$document /* = null || !$request->input("access_hash") || $document->access_hash != $request->input("access_hash")*/)
                return ApiController::createJsonResponseStatic("The document does not exists or the access token is wrong", true, 400);
            return $next($request);
        }
        return ApiController::createJsonResponseStatic("The document does not exists or the access token is wrong", true, 400);
    }
}
