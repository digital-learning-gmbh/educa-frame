<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Exception Email Enabled
    |--------------------------------------------------------------------------
    |
    | Enable/Disable exception email notifications
    |
    */

    'emailExceptionEnabled'        => env('EMAIL_EXCEPTION_ENABLED',false),
    'emailExceptionEnabledDefault' => false,

    /*
    |--------------------------------------------------------------------------
    | Exception Email From
    |--------------------------------------------------------------------------
    |
    | This is the email your exception will be from.
    |
    */

    'emailExceptionFrom'        => env('EMAIL_EXCEPTION_FROM','educa@ibadual.com'),
    'emailExceptionFromDefault' => 'educa@ibadual.com',

    /*
    |--------------------------------------------------------------------------
    | Exception Email To
    |--------------------------------------------------------------------------
    |
    | This is the email(s) the exceptions will be emailed to.
    |
    */

    'emailExceptionsTo'        => env('EMAIL_EXCEPTION_TO'),
    'emailExceptionsToDefault' => 'reporting@digitallearning.gmbh',

    /*
    |--------------------------------------------------------------------------
    | Exception Email CC
    |--------------------------------------------------------------------------
    |
    | This is the email(s) the exceptions will be CC emailed to.
    |
    */

    'emailExceptionCCto'        => env('EMAIL_EXCEPTION_CC'),
    'emailExceptionCCtoDefault' => [],

    /*
    |--------------------------------------------------------------------------
    | Exception Email BCC
    |--------------------------------------------------------------------------
    |
    | This is the email(s) the exceptions will be BCC emailed to.
    |
    */

    'emailExceptionBCCto'        => env('EMAIL_EXCEPTION_BCC'),
    'emailExceptionBCCtoDefault' => [],

    /*
    |--------------------------------------------------------------------------
    | Exception Email Subject
    |--------------------------------------------------------------------------
    |
    | This is the subject of the exception email
    |
    */

    'emailExceptionSubject'        => env('EMAIL_EXCEPTION_SUBJECT'),
    'emailExceptionSubjectDefault' => 'Error on '.config('app.env'),

    /*
    |--------------------------------------------------------------------------
    | Exception Email View
    |--------------------------------------------------------------------------
    |
    | This is the view that will be used for the email.
    |
    */

    'emailExceptionView' => 'emails.exception',

];
