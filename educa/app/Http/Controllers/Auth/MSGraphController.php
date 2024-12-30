<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\AppServiceProvider;
use Dcblogdev\MsGraph\Facades\MsGraph;
use Illuminate\Support\Facades\Session;

class MSGraphController extends Controller
{
    public function connect(\Illuminate\Http\Request $request)
    {
        $tenant = AppServiceProvider::getTenant();

        // override config
        config(['msgraph.clientId' => $tenant->ms_graph_client_id]);
        config(['msgraph.clientSecret' => $tenant->ms_graph_secret_id]);
        config(['msgraph.urlAuthorize' => 'https://login.microsoftonline.com/'.$tenant->ms_graph_tenant_id.'/oauth2/v2.0/authorize']);
        config(['msgraph.urlAccessToken' => 'https://login.microsoftonline.com/'.$tenant->ms_graph_tenant_id.'/oauth2/v2.0/token']);
        config(['msgraph.redirectUri' => 'https://'.$tenant->domain.'/msgraph/oauth']);
        config(['msgraph.msgraphLandingUri' => 'https://'.$tenant->domain.'/msgraph/oauth']);

        try {
            auth('cloud')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            Session::flush();
            Session::start();
        } catch (\Exception $exception) {}
        return MsGraph::connect();
    }

    public function connectMobile(\Illuminate\Http\Request $request)
    {
        $tenant = AppServiceProvider::getTenant();

        // override config
        config(['msgraph.clientId' => $tenant->ms_graph_client_id]);
        config(['msgraph.clientSecret' => $tenant->ms_graph_secret_id]);
        config(['msgraph.urlAuthorize' => 'https://login.microsoftonline.com/'.$tenant->ms_graph_tenant_id.'/oauth2/v2.0/authorize']);
        config(['msgraph.urlAccessToken' => 'https://login.microsoftonline.com/'.$tenant->ms_graph_tenant_id.'/oauth2/v2.0/token']);
        config(['msgraph.redirectUri' => 'https://'.$tenant->domain.'/msgraph/oauth']);
        config(['msgraph.msgraphLandingUri' => 'https://'.$tenant->domain.'/msgraph/oauth']);

        try {
            auth('cloud')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            Session::flush();
            Session::start();
        } catch (\Exception $exception) {}
        //    config(["msgraph.redirectUri" => $request->getSchemeAndHttpHost()."/msgraph/oauthMobile",
        //       "msgraph.msgraphLandingUri" => $request->getSchemeAndHttpHost()."/msgraph/oauthMobile"
        //   ]);
        // (new MsGraphServiceProvider(app()))->register();
        return MsGraph::connect();
    }
}
