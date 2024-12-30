<?php

namespace App\Http\Controllers\Auth;

use App\CloudID;
use App\Http\Controllers\Controller;
use App\SystemEinstellung;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function reset(Request $request)
    {
        $request->validate( [
        'email' => 'required|email',
         ], $this->validationErrorMessages());

        $cloudUser = CloudID::where('email','=', $request->input("email"))->first();
        if($cloudUser != null && $cloudUser->loginType == "eloquent")
        {
            $realPasswort = str_random(10);
            $cloudUser->password = bcrypt($realPasswort);
            $cloudUser->save();
            $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
            $beautymail->send("emails.resetPassword", ["user" => $cloudUser, "password" => $realPasswort], function($message) use ($cloudUser)
            {
                $message
                    ->to($cloudUser->email, $cloudUser->name)
                    ->subject('educa - Dein Passwort wurde zurückgesetzt');
            });
        }
        $systemMessage = SystemEinstellung::getEinstellungen("system.message", "");
        return view('auth.passwords.resetSend',["systemMessage" => $systemMessage]);
    }

    public function showResetForm(Request $request, $token = null)
    {
        $systemMessage = SystemEinstellung::getEinstellungen("system.message", "");
        return view('auth.passwords.reset',["systemMessage" => $systemMessage,  'token' => $token, 'email' => $request->email]);
    }
}
