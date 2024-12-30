<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'cloud',
        'passwords' => 'clouds',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [
        'verwaltung' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'dozent' => [
            'driver' => 'session',
            'provider' => 'dozents',
        ],

        'unternehmen' => [
            'driver' => 'session',
            'provider' => 'unternehmens',
        ],

        'cloud' => [
            'driver' => 'session',
            'provider' => 'clouds',
        ],

        'clouds_employee' => [
            'driver' => 'session',
            'provider' => 'clouds_employee',
        ],

        'clouds_student' => [
            'driver' => 'session',
            'provider' => 'clouds_student',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'clouds',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\User::class,
        ],

        'clouds' => [
            'driver' => 'educacloud',
            'model' => App\CloudID::class,
            'mode' => 'failAll',
            'subprovider' => [
                'clouds_database' => [
                    'provider' => 'clouds_database',
                    'username' => 'email',
                    'password' => 'password',
                ],
            ]
        ],

        'clouds_database' => [
            'driver' => 'eloquent',
            'model' => App\CloudID::class,
        ],

        'clouds_employee' => [
            'driver' => 'ldap',
            'model' => App\Ldap\Empolyee\User::class,
            'database' => [
                'model' => App\CloudID::class,
                'sync_passwords' => false,
                'sync_attributes' => [
                    \App\Ldap\Empolyee\AttributeHandler::class,
                ]
            ],
        ],

        'clouds_student' => [
            'driver' => 'ldap',
            'model' => App\Ldap\Student\User::class,
            'database' => [
                'model' => App\CloudID::class,
                'sync_passwords' => false,
                'sync_attributes' => [
                    \App\Ldap\Student\AttributeHandler::class,
                ]
            ],
        ],

        'dozents' => [
            'driver' => 'eloquent',
            'model' => App\Lehrer::class,
        ],

        'unternehmens' => [
            'driver' => 'eloquent',
            'model' => App\Kontakt::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
        ],
    ],

];
