<?php

namespace App\Providers;

use App\Models\Tenant;
use App\Schule;
use App\Schuljahr;
use App\SchuljahrEntwurf;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    protected static $global_school;
    protected static $global_year;
    protected static $global_entwurf;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ConvertEmptyStringsToNull::skipWhen(function (Request $request){
            return str_contains($request->url(),"h5p");
        });

        try {
        $tenant = AppServiceProvider::getTenant();

        config(['msgraph.clientId' => $tenant->ms_graph_client_id]);
        config(['msgraph.clientSecret' => $tenant->ms_graph_secret_id]);
        config(['msgraph.urlAuthorize' => 'https://login.microsoftonline.com/'.$tenant->ms_graph_tenant_id.'/oauth2/v2.0/authorize']);
        config(['msgraph.urlAccessToken' => 'https://login.microsoftonline.com/'.$tenant->ms_graph_tenant_id.'/oauth2/v2.0/token']);
        config(['msgraph.redirectUri' => 'https://'.$tenant->domain.'/msgraph/oauth']);
        config(['msgraph.msgraphLandingUri' => 'https://'.$tenant->domain.'/msgraph/oauth']);
        } catch(\Exception $exception)
        {
            Log::warning("cloud not override msgraph configuration");
        }

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public static function schoolTranslation($expression, $default)
    {
        $school = self::getSchool();
        return $school->getEinstellungen($expression,$default);
    }

    public static function getSchool($defaultSchool = null)
    {
        if(Session::has('school_id'))
        {
            // check if the dozenz is an der schule
            $guard = "verwaltung";
            if(\Illuminate\Support\Facades\Request::is("dozent/*"))
            {
                $guard = "dozent";
            }
            if( Auth::guard($guard)->user() != null
                && Auth::guard($guard)->user()->schulen != null
                && !in_array(Session::get("school_id"), Auth::guard($guard)->user()->schulen->pluck("id")->toArray())
            ) {
                Session::remove("school_id");
                Session::remove("year_id");
                Session::remove("entwurf_id");
                return self::getSchool($defaultSchool);
            }
            $global_school = Schule::findOrFail(Session::get("school_id"));
        } else {
            if($defaultSchool == null)
            {
                $cloudUser = Session::get('cloud_user');
                $id = $cloudUser->getAppEinstellung("defaultSchool","stupla",Auth::guard('verwaltung')->user()->schulen->first()->id);
                $global_school = Schule::findOrFail($id);
            } else {
                $global_school = $defaultSchool;
            }
            Session::put('school_id', $global_school->id);
        }
        return $global_school;
    }

    public static function getEntwurf()
    {
        if(Session::has('entwurf_id'))
        {
            $global_entwurf = SchuljahrEntwurf::findOrFail(Session::get("entwurf_id"));
            if($global_entwurf->schuljahr_id != Session::has('year_id'))
            {
                Session::remove("entwurf_id");
                $global_entwurf = self::getEntwurf();
            }
        } else {
            $global_entwurf = AppServiceProvider::getSchoolYear()->entwurfe()->first();
            Session::put('entwurf_id', $global_entwurf->id);
        }
        return $global_entwurf;
    }

    public static function getSchoolYear()
    {
        if(Session::has('year_id'))
        {
            $global_year = Schuljahr::find(Session::get("year_id"));
            if($global_year == null || $global_year->schule_id != Session::has('school_id'))
            {
                Session::remove("year_id");
                $global_year = self::getSchoolYear();
            }
        } else {
            // we should use here the current (real) schuljhar
            $global_year = AppServiceProvider::getSchool()->getCurrentSchoolYear();
            Session::put('year_id', $global_year->id);
        }
        return $global_year;
    }

    /**
     * @throws \Exception
     */
    public static function getTenant()
    {
        $tenant = Tenant::where("domain","=",request()->getHost())->first();

        if($tenant == null)
        {
            // load default tenant
            $tenant = Tenant::where("isFallBackTenant","=",1)->first();
            if($tenant == null)
            {
                throw new \Exception("Fatal error; no tenant configuration found and also no default fallback. We have to say goodbye.");
            }
        }

        $chat = str_replace("https://","",config('laravel-rocket-chat.instance'));
        $tenant->chat = $chat;
        return $tenant;
    }
}
