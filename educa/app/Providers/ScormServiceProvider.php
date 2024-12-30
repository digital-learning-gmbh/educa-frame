<?php

namespace App\Providers;

use App\Http\Controllers\API\V1\SCORM\Contracts\ScormQueryServiceContract;
use App\Http\Controllers\API\V1\SCORM\Contracts\ScormRepositoryContract;
use App\Http\Controllers\API\V1\SCORM\Contracts\ScormServiceContract;
use App\Http\Controllers\API\V1\SCORM\Contracts\ScormTrackServiceContract;
use App\Http\Controllers\API\V1\SCORM\ScormQueryService;
use App\Http\Controllers\API\V1\SCORM\ScormRepository;
use App\Http\Controllers\API\V1\SCORM\ScormService;

use App\Http\Controllers\API\V1\SCORM\ScormTrackService;
use Illuminate\Support\ServiceProvider;


class ScormServiceProvider extends ServiceProvider
{
    public $singletons = [
        ScormServiceContract::class => ScormService::class,
        ScormQueryServiceContract::class => ScormQueryService::class,
        ScormTrackServiceContract::class => ScormTrackService::class,
        ScormRepositoryContract::class => ScormRepository::class,
    ];

    public function boot()
    {
        //
    }
}
