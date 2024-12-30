<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocalController extends Controller
{
    public function changeLocale(Request $request)
    {

        session()->put('current_locale', $request->input('locale','de'));

       return redirect()->back();
    }
}
