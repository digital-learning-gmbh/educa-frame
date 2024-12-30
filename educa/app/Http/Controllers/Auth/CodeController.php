<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\SystemEinstellung;
use Illuminate\Http\Request;

class CodeController extends Controller
{
    public function showCodeForm(Request $request, $token = null)
    {
        $systemMessage = SystemEinstellung::getEinstellungen("system.message", "");
        return view('auth.code',["systemMessage" => $systemMessage,  'token' => $token, 'code' => $request->input("code")]);
    }
}
