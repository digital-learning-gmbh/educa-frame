<?php

namespace StuPla\CloudSDK\Permission;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use StuPla\CloudSDK\Permission\Contracts\Permission;
use StuPla\CloudSDK\Permission\Contracts\Role;
use StuPla\CloudSDK\Permission\PermissionRegistrar;
use StuPla\CloudSDK\Permission\PermissionServiceProvider;

abstract class TestCase extends Orchestra
{
    /** @var \StuPla\CloudSDK\Permission\Test\User */
    protected $testUser;

    /** @var \StuPla\CloudSDK\Permission\Test\Admin */
    protected $testAdmin;

    /** @var \StuPla\CloudSDK\Permission\Models\Role */
    protected $testUserRole;

    /** @var \StuPla\CloudSDK\Permission\Models\Role */
    protected $testAdminRole;

    /** @var \StuPla\CloudSDK\Permission\Models\Permission */
    protected $testUserPermission;

    /** @var \StuPla\CloudSDK\Permission\Models\Permission */
    protected $testAdminPermission;

    public function setUp(): void
    {
        parent::setUp();

        // Note: this also flushes the cache from within the migration
        $this->setUpDatabase($this->app);

        $this->testUser = User::first();
        $this->testUserRole = app(Role::class)->find(1);
        $this->testUserPermission = app(Permission::class)->find(1);

        $this->testAdmin = Admin::first();
        $this->testAdminRole = app(Role::class)->find(3);
        $this->testAdminPermission = app(Permission::class)->find(4);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            PermissionServiceProvider::class,
        ];
    }

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('view.paths', [__DIR__.'/resources/views']);

        // Set-up admin guard
        $app['config']->set('auth.guards.admin', ['driver' => 'session', 'provider' => 'admins']);
        $app['config']->set('auth.providers.admins', ['driver' => 'eloquent', 'model' => Admin::class]);

        // Use test User model for users provider
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('cache.prefix', 'spatie_tests---');
    }

    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['config']->set('permission.column_names.model_morph_key', 'model_test_id');

        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->softDeletes();
        });

        $app['db']->connection()->getSchemaBuilder()->create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
        });

        include_once __DIR__.'/../../src/database/migrations/create_permission_tables.php.stub';

        (new \CreatePermissionTables())->up();

        User::create(['email' => 'test@user.com']);
        Admin::create(['email' => 'admin@user.com']);
        $app[Role::class]->create(['name' => 'testRole']);
        $app[Role::class]->create(['name' => 'testRole2']);
        $app[Role::class]->create(['name' => 'testAdminRole', 'guard_name' => 'admin']);
        $app[Permission::class]->create(['name' => 'edit-articles']);
        $app[Permission::class]->create(['name' => 'edit-news']);
        $app[Permission::class]->create(['name' => 'edit-blog']);
        $app[Permission::class]->create(['name' => 'admin-permission', 'guard_name' => 'admin']);
        $app[Permission::class]->create(['name' => 'Edit News']);
    }

    /**
     * Reload the permissions.
     */
    protected function reloadPermissions()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function createCacheTable()
    {
        Schema::create('cache', function ($table) {
            $table->string('key')->unique();
            $table->text('value');
            $table->integer('expiration');
        });
    }
}
