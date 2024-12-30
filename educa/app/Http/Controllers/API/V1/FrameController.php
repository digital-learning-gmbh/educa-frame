<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrameController extends ApiController
{
    public function frameConfiguration(Request $request)
    {
        return response()->json(config('educa-frame.pages'));
    }
}
