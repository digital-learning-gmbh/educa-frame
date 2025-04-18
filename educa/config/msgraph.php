<?php


return [

    /*
    * the clientId is set from the Microsoft portal to identify the application
    * https://apps.dev.microsoft.com
    */
    'clientId' => env('MSGRAPH_CLIENT_ID'),

    /*
    * set the application secret
    */

    'clientSecret' => env('MSGRAPH_SECRET_ID'),

    /*
    * Set the url to trigger the oauth process this url should call return MsGraph::connect();
    */
    'redirectUri' => env('MSGRAPH_OAUTH_URL',env("APP_URL")."/msgraph/oauth"),

    /*
    * set the url to be redirected to once the token has been saved
    */

    'msgraphLandingUri'  => env('MSGRAPH_LANDING_URL',env("APP_URL")."/msgraph/oauth"),

    /*
    set the tenant authorize url
    */

    'tenantUrlAuthorize' => env('MSGRAPH_TENANT_AUTHORIZE'),

    /*
    set the tenant token url
    */
    'tenantUrlAccessToken' => env('MSGRAPH_TENANT_TOKEN'),

    /*
    set the authorize url
    */
    'urlAuthorize' => 'https://login.microsoftonline.com/'.env('MSGRAPH_TENANT_ID').'/oauth2/v2.0/authorize',

    /*
    set the token url
    */
    'urlAccessToken' => 'https://login.microsoftonline.com/'.env('MSGRAPH_TENANT_ID').'/oauth2/v2.0/token',

    /*
    set the scopes to be used, Microsoft Graph API will accept up to 20 scopes
    */

    'scopes' => 'user.read',

    /*
    The default timezone is set to Europe/London this option allows you to set your prefered timetime
    */
    'preferTimezone' => env('MSGRAPH_PREFER_TIMEZONE', 'outlook.timezone="Europe/Berlin"'),
];
