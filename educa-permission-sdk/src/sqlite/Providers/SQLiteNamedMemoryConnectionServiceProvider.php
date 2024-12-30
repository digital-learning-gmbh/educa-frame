<?php

namespace StuPla\CloudSDK\sqlite\Providers;


use StuPla\CloudSDK\sqlite\Database\Connectors\SQLiteConnector;
use StuPla\CloudSDK\sqlite\Database\SQLiteConnection;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class SQLiteNamedMemoryConnectionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('db.connector.sqlite-named', SQLiteConnector::class);

        Connection::resolverFor('sqlite-named', static function ($connection, $database, $prefix, $config) {
            return new SQLiteConnection($connection, $database, $prefix, $config);
        });
    }
}