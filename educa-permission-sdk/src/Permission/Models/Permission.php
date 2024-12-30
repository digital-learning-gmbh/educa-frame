<?php

namespace StuPla\CloudSDK\Permission\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use StuPla\CloudSDK\Permission\Contracts\Permission as PermissionContract;
use StuPla\CloudSDK\Permission\Exceptions\PermissionAlreadyExists;
use StuPla\CloudSDK\Permission\Exceptions\PermissionDoesNotExist;
use StuPla\CloudSDK\Permission\Guard;
use StuPla\CloudSDK\Permission\PermissionRegistrar;
use StuPla\CloudSDK\Permission\Scope;
use StuPla\CloudSDK\Permission\Traits\HasRoles;
use StuPla\CloudSDK\Permission\Traits\RefreshesPermissionCache;

class Permission extends Model implements PermissionContract
{
    use HasRoles;
    use RefreshesPermissionCache;

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        $attributes['scope_name'] = $attributes['scope_name'] ?? Scope::getDefaultName();

        parent::__construct($attributes);
    }

    public function getTable()
    {
        return config('permission.table_names.permissions', parent::getTable());
    }

    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);
        $attributes['scope_name'] = $attributes['scope_name'] ?? Scope::getDefaultName();

        $permission = static::getPermissions(['name' => $attributes['name'], 'guard_name' => $attributes['guard_name'], 'scope_name' => $attributes['scope_name']])->first();

        if ($permission) {
            throw PermissionAlreadyExists::create($attributes['name'], $attributes['guard_name'], $attributes['scope_name']);
        }

        return static::query()->create($attributes);
    }

    /**
     * A permission can be applied to roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.role'),
            config('permission.table_names.role_has_permissions'),
            'permission_id',
            'role_id'
        );
    }

    /**
     * A permission belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'model',
            config('permission.table_names.model_has_permissions'),
            'permission_id',
            config('permission.column_names.model_morph_key')
        );
    }

    /**
     * Find a permission by its name (and optionally guardName).
     *
     * @param string $name
     * @param string|null $guardName
     * @param string|null $scope
     *
     * @throws \StuPla\CloudSDK\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return \StuPla\CloudSDK\Permission\Contracts\Permission
     */
    public static function findByName(string $name, $guardName = null, $scope = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $scope = $scope ?? Scope::getDefaultName();
        $permission = static::getPermissions(['name' => $name, 'guard_name' => $guardName, 'scope_name' => $scope])->first();
        if (! $permission) {
            throw PermissionDoesNotExist::create($name, $guardName);
        }

        return $permission;
    }

    /**
     * Find a permission by its id (and optionally guardName).
     *
     * @param int $id
     * @param string|null $guardName
     * @param string|null $scope
     *
     * @throws \StuPla\CloudSDK\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return \StuPla\CloudSDK\Permission\Contracts\Permission
     */
    public static function findById(int $id, $guardName = null, $scope = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $scope = $scope ?? Scope::getDefaultName();
        $permission = static::getPermissions(['id' => $id, 'guard_name' => $guardName, 'scope_name' => $scope])->first();

        if (! $permission) {
            throw PermissionDoesNotExist::withId($id, $guardName);
        }

        return $permission;
    }

    /**
     * Find or create permission by its name (and optionally guardName).
     *
     * @param string $name
     * @param string|null $guardName
     * @param string|null $scope
     *
     * @return \StuPla\CloudSDK\Permission\Contracts\Permission
     */
    public static function findOrCreate(string $name, $guardName = null, $scope = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $scope = $scope ?? Scope::getDefaultName();
        $permission = static::getPermissions(['name' => $name, 'guard_name' => $guardName, 'scope_name' => $scope])->first();

        if (! $permission) {
            return static::query()->create(['name' => $name, 'guard_name' => $guardName, 'scope' => $scope]);
        }

        return $permission;
    }

    /**
     * Get the current cached permissions.
     */
    protected static function getPermissions(array $params = []): Collection
    {
        return app(PermissionRegistrar::class)
            ->setPermissionClass(static::class)
            ->getPermissions($params);
    }
}
