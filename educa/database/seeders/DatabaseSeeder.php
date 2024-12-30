<?php

namespace Database\Seeders;
use App\Http\Controllers\Verwaltung\Technical\InfoController;
use App\Models\LearnContentCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Observers\FeedObserver::$shouldFakeData = true;
        $this->call(TenantSeeder::class);
        // This is a basic installation and should be executed on each installation!
        // Ground Base Installation
        $this->call(GroupAppsSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(CloudIDSeeder::class);
    }
}
