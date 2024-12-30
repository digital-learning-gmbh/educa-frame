<?php

namespace App\Listeners;

use App\CloudID;
use App\Providers\AppServiceProvider;
use Dcblogdev\MsGraph\Models\MsGraphToken;
use Illuminate\Support\Facades\Auth;
use StuPla\CloudSDK\Permission\Models\Role;

class NewMicrosoft365SignInListener
{
    public function handle($event)
    {
        $tokenId = $event->token['token_id'];
        $token = MsGraphToken::where("id","=",$tokenId)->first();
        if($token == null)
            return redirect("/");

        if ($token->user_id == null) {
            $cloudId = CloudID::withTrashed()->whereRaw("email LIKE ?",trim(strtolower($event->token['info']['userPrincipalName'])))->first();
            if($cloudId != null)
            {
                $token->user_id = $cloudId->id;
                $token->save();

                $cloudId->deleted_at = null;
                $cloudId->save();

                $tenant = AppServiceProvider::getTenant();
                if($tenant != null && $tenant->roleRegister != null && Role::where("id","=",$tenant->roleRegister)->first() != null)
                {
                    $cloudId->roles()->sync([Role::where("id","=",$tenant->roleRegister)->first()->id], false);
                }

                Auth::guard('cloud')->loginUsingId($cloudId->id);
            } else {
                $cloudId = CloudID::create([
                    'name' => $event->token['info']['displayName'],
                    'email' => $event->token['info']['userPrincipalName'],
                    'password' => '',
                    'deleted_at' => null
                ]);

                $token->user_id = $cloudId->id;
                $token->save();

                $tenant = AppServiceProvider::getTenant();
                if($tenant != null && $tenant->roleRegister != null && Role::where("id","=",$tenant->roleRegister)->first() != null)
                {
                    $cloudId->roles()->sync([Role::where("id","=",$tenant->roleRegister)->first()->id], false);
                }

                if(AppServiceProvider::getTenant()->id != null) {
                    $cloudId->tenants()->sync([AppServiceProvider::getTenant()->id]);
                }
                Auth::guard('cloud')->loginUsingId($cloudId->id);
            }
        } else {
            $user = CloudID::findOrFail($token->user_id);
            $user->deleted_at = null;
            $user->save();

            Auth::guard('cloud')->loginUsingId($user->id);
        }
    }
}
