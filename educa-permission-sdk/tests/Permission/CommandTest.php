<?php

namespace StuPla\CloudSDK\Permission;

use Illuminate\Support\Facades\Artisan;
use StuPla\CloudSDK\Permission\Models\Permission;
use StuPla\CloudSDK\Permission\Models\Role;

class CommandTest extends TestCase
{
    /** @test */
    public function it_can_create_a_role()
    {
        Artisan::call('permission:create-role', ['name' => 'new-role']);

        $this->assertCount(1, Role::where('name', 'new-role')->get());
        $this->assertCount(0, Role::where('name', 'new-role')->first()->permissions);
    }

    /** @test */
    public function it_can_create_a_role_with_a_specific_guard()
    {
        Artisan::call('permission:create-role', [
            'name' => 'new-role',
            'guard' => 'api',
        ]);

        $this->assertCount(1, Role::where('name', 'new-role')
            ->where('guard_name', 'api')
            ->get());
    }

    /** @test */
    public function it_can_create_a_permission()
    {
        Artisan::call('permission:create-permission', ['name' => 'new-permission']);

        $this->assertCount(1, Permission::where('name', 'new-permission')->get());
    }

    /** @test */
    public function it_can_create_a_permission_with_a_specific_guard()
    {
        Artisan::call('permission:create-permission', [
            'name' => 'new-permission',
            'guard' => 'api',
        ]);

        $this->assertCount(1, Permission::where('name', 'new-permission')
            ->where('guard_name', 'api')
            ->get());
    }

    /** @test */
    public function it_can_create_a_role_and_permissions_at_same_time()
    {
        Artisan::call('permission:create-role', [
            'name' => 'new-role',
            'permissions' => 'first permission | second permission',
        ]);

        $role = Role::where('name', 'new-role')->first();

        $this->assertTrue($role->hasPermissionTo('first permission'));
        $this->assertTrue($role->hasPermissionTo('second permission'));
    }

    /** @test */
    public function it_can_create_a_role_without_duplication()
    {
        Artisan::call('permission:create-role', ['name' => 'new-role']);
        Artisan::call('permission:create-role', ['name' => 'new-role']);

        $this->assertCount(1, Role::where('name', 'new-role')->get());
        $this->assertCount(0, Role::where('name', 'new-role')->first()->permissions);
    }

    /** @test */
    public function it_can_create_a_permission_without_duplication()
    {
        Artisan::call('permission:create-permission', ['name' => 'new-permission']);
        Artisan::call('permission:create-permission', ['name' => 'new-permission']);

        $this->assertCount(1, Permission::where('name', 'new-permission')->get());
    }

    /** @test */
    public function it_can_show_permission_tables()
    {
        Artisan::call('permission:show');

        $output = Artisan::output();

        $this->assertTrue(strpos($output, 'Guard: web') !== false);
        $this->assertTrue(strpos($output, 'Guard: admin') !== false);

        if (method_exists($this, 'assertMatchesRegularExpression')) {
            // |               | testRole | testRole2 |
            $this->assertMatchesRegularExpression('/\|\s+\|\s+testRole\s+\|\s+testRole2\s+\|/', $output);

            // | edit-articles |  ·       |  ·        |
            $this->assertMatchesRegularExpression('/\|\s+edit-articles\s+\|\s+·\s+\|\s+·\s+\|/', $output);
        } else { // phpUnit 9/8
            $this->assertRegExp('/\|\s+\|\s+testRole\s+\|\s+testRole2\s+\|/', $output);
            $this->assertRegExp('/\|\s+edit-articles\s+\|\s+·\s+\|\s+·\s+\|/', $output);
        }

        Role::findByName('testRole')->givePermissionTo('edit-articles');
        $this->reloadPermissions();

        Artisan::call('permission:show');

        $output = Artisan::output();

        // | edit-articles |  ·       |  ·        |
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression('/\|\s+edit-articles\s+\|\s+✔\s+\|\s+·\s+\|/', $output);
        } else {
            $this->assertRegExp('/\|\s+edit-articles\s+\|\s+✔\s+\|\s+·\s+\|/', $output);
        }
    }

    /** @test */
    public function it_can_show_permissions_for_guard()
    {
        Artisan::call('permission:show', ['guard' => 'web']);

        $output = Artisan::output();

        $this->assertTrue(strpos($output, 'Guard: web') !== false);
        $this->assertTrue(strpos($output, 'Guard: admin') === false);
    }
}
