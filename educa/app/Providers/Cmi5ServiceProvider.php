<?php

namespace App\Providers;

use App\Http\Controllers\API\V1\Cmi5\Cmi5Service;
use App\Http\Controllers\API\V1\Cmi5\Cmi5UploadService;
use App\Http\Controllers\API\V1\Cmi5\Contracts\Cmi5ServiceContract;
use App\Http\Controllers\API\V1\Cmi5\Contracts\Cmi5UploadServiceContract;
use App\Http\Controllers\API\V1\Cmi5\Repositories\Cmi5AuRepository;
use App\Http\Controllers\API\V1\Cmi5\Repositories\Cmi5Repository;
use App\Http\Controllers\API\V1\Cmi5\Repositories\Contracts\Cmi5AuRepositoryContract;
use App\Http\Controllers\API\V1\Cmi5\Repositories\Contracts\Cmi5RepositoryContract;
use Illuminate\Support\ServiceProvider;


class Cmi5ServiceProvider extends ServiceProvider
{
    public $singletons = [
        Cmi5ServiceContract::class => Cmi5Service::class,
        Cmi5RepositoryContract::class => Cmi5Repository::class,
        Cmi5AuRepositoryContract::class => Cmi5AuRepository::class,
        Cmi5UploadServiceContract::class => Cmi5UploadService::class,
    ];

    public function boot()
    {
        //
    }
}
