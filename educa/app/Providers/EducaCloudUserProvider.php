<?php

namespace App\Providers;

use App\CloudID;
use App\Models\SessionToken;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use LdapRecord\Laravel\Auth\UserProvider;
use LdapRecord\Laravel\LdapUserRepository;

class EducaCloudUserProvider implements \Illuminate\Contracts\Auth\UserProvider
{
    private $providers = [];
    private $providerConfig = [];
    private $mode = 'failAll';

    public function __construct(HasherContract $hasher, $model, $subprovider, $mode)
    {
        $this->mode = $mode;
        foreach ($subprovider as $key=>$providers)
        {
            $providerInstance = Auth::createUserProvider($providers['provider']);
            if($providerInstance != null) {
                $this->providerConfig[$key] = $providers;
                $this->providers[$key] = $providerInstance;
            }
        }
    }

    public function retrieveById($identifier)
    {
        foreach ($this->providerConfig as $key=>$providerConfig) {
            $result = $this->providers[$key]->retrieveById($identifier);
            if($result != null)
                return $result;
        }
        return null;
    }

    public function retrieveByToken($identifier, $token)
    {
        foreach ($this->providerConfig as $key=>$providerConfig) {
            $result = $this->providers[$key]->retrieveByToken($identifier, $token);
            if($result != null)
                return $result;
        }
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        foreach ($this->providerConfig as $key=>$providerConfig) {
            $result = $this->providers[$key]->updateRememberToken($user, $token);
            if($result != null)
                return $result;
        }
        return null;
    }

    public function retrieveByCredentials(array $credentials)
    {
        if(array_key_exists("api_token",$credentials))
        {
            $token = SessionToken::where("token","LIKE", $credentials['api_token'])->first();
            return $token->user;
        }
        foreach ($this->providerConfig as $key=>$providerConfig) {
            $providerCredentials = [
            ];
            if (array_key_exists("email",$credentials)) {
                $providerCredentials = [
                    $providerConfig['username'] => $credentials['email'],
                    $providerConfig['password'] => $credentials['password'],
                ];
            }
            $result = $this->providers[$key]->retrieveByCredentials($providerCredentials);
            // print_r($key." : ". $result); // TODO make log message
            if($result != null)
                return $result;
        }
        return null;
    }

    public function validateCredentials(Authenticatable $model, array $credentials)
    {
        // print_r(get_class($model));
        foreach ($this->providerConfig as $key=>$providerConfig) {
            $providerCredentials = [
                $providerConfig['username'] => $credentials['email'],
                $providerConfig['password'] => $credentials['password'],
            ];
            $result = $this->providers[$key]->validateCredentials($model, $providerCredentials);
           // print_r($key." : ". $result); // TODO make log message
            if($result == 1)
                return $result;
        }
        return false;
    }
}
