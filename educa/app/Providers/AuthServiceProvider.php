<?php

namespace App\Providers;

use Adldap\Laravel\Auth\DatabaseUserProvider;
use RuntimeException;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('educacloud', function ($app, array $config) {
            return $this->makeUserProvider($app['hash'], $config);
        });
        //
    }

    protected function makeUserProvider(Hasher $hasher, array $config)
    {
        $provider = EducaCloudUserProvider::class;

        // The DatabaseUserProvider requires a model to be configured
        // in the configuration. We will validate this here.
        if (is_a($provider, EducaCloudUserProvider::class, $allowString = true)) {
            // We will try to retrieve their model from the config file,
            // otherwise we will try to use the providers config array.
            $model = Config::get('ldap_auth.model') ?? Arr::get($config, 'model');

            if (! $model) {
                throw new RuntimeException(
                    "No model is configured. You must configure a model to use with the {$provider}."
                );
            }


            $subprovider = Arr::get($config, 'subprovider');
            $mode = Arr::get($config, 'mode');

            return new $provider($hasher, $model, $subprovider, $mode);
        }

        return new $provider();
    }
}
