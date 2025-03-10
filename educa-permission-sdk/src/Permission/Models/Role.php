<?php

namespace StuPla\CloudSDK\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use StuPla\CloudSDK\Permission\Contracts\Role as RoleContract;
use StuPla\CloudSDK\Permission\Exceptions\GuardDoesNotMatch;
use StuPla\CloudSDK\Permission\Exceptions\RoleAlreadyExists;
use StuPla\CloudSDK\Permission\Exceptions\RoleDoesNotExist;
use StuPla\CloudSDK\Permission\Guard;
use StuPla\CloudSDK\Permission\Scope;
use StuPla\CloudSDK\Permission\Traits\HasPermissions;
use StuPla\CloudSDK\Permission\Traits\HasRoles;
use StuPla\CloudSDK\Permission\Traits\RefreshesPermissionCache;

class Role extends Model implements RoleContract
{
    use HasRoles;
    use RefreshesPermissionCache;

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        $attributes['scope_name'] = $attributes['scope_name'] ?? Scope::getDefaultName();
        $attributes['scope_id'] = $attributes['scope_id'] ?? Scope::getDefaultId();

        parent::__construct($attributes);
    }

    public function getTable()
    {
        return config('permission.table_names.roles', parent::getTable());
    }

    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);
        $attributes['scope_name'] = $attributes['scope_name'] ?? Scope::getDefaultName();
        $attributes['scope_id'] = $attributes['scope_id'] ??  Scope::getDefaultId();

        if (static::where('name', $attributes['name'])->where('guard_name', $attributes['guard_name'])->where('scope_name', $attributes['scope_name'])->where('scope_id', $attributes['scope_id'])->first()) {
            throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name'], $attributes['scope_name'], $attributes['scope_id']);
        }

        return static::query()->create($attributes);
    }

    /**
     * A role may be given various permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            'role_id',
            'permission_id'
        );
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'model',
            config('permission.table_names.model_has_roles'),
            'role_id',
            config('permission.column_names.model_morph_key')
        );
    }

    /**
     * Find a role by its name and guard name.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return \StuPla\CloudSDK\Permission\Contracts\Role|\StuPla\CloudSDK\Permission\Models\Role
     *
     * @throws \StuPla\CloudSDK\Permission\Exceptions\RoleDoesNotExist
     */
    public static function findByName(string $name, $guardName = null, $scopeName = null, $scopeId = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $scopeName = $scopeName ?? Scope::getDefaultName();
        $scopeId = $scopeId ?? Scope::getDefaultId();

        $role = static::where('name', $name)->where('guard_name', $guardName)->where('scope_name', $scopeName)->where('scope_id', $scopeId)->first();

        if (! $role) {
            throw RoleDoesNotExist::named($name);
        }

        return $role;
    }

    public static function findById(int $id, $guardName = null, $scopeName = null, $scopeId = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $scopeName = $scopeName ?? Scope::getDefaultName();
        $scopeId = $scopeId ?? Scope::getDefaultId();

        $role = static::where('id', $id)->where('guard_name', $guardName)->where('scope_name', $scopeName)->where('scope_id', $scopeId)->first();

        if (! $role) {
            throw RoleDoesNotExist::withId($id);
        }

        return $role;
    }

    /**
     * Find or create role by its name (and optionally guardName).
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return \StuPla\CloudSDK\Permission\Contracts\Role
     */
    public static function findOrCreate(string $name, $guardName = null, $scopeName = null, $scopeId = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $scopeName = $scopeName ?? Scope::getDefaultName();
        $scopeId = $scopeId ?? Scope::getDefaultId();

        $role = static::where('name', $name)->where('guard_name', $guardName)->where('scope_name', $scopeName)->where('scope_id', $scopeId)->first();

        if (! $role) {
            return static::query()->create(['name' => $name, 'guard_name' => $guardName, 'scope_name' => $scopeName, 'scope_id' => $scopeId]);
        }

        return $role;
    }

    /**
     * Determine if the user may perform the given permission.
     *
     * @param string|Permission $permission
     *
     * @return bool
     *
     * @throws \StuPla\CloudSDK\Permission\Exceptions\GuardDoesNotMatch
     */
    public function hasPermissionTo($permission): bool
    {
        if (config('permission.enable_wildcard_permission', false)) {
            return $this->hasWildcardPermission($permission, $this->getDefaultGuardName());
        }

        $permissionClass = $this->getPermissionClass();

        if (is_string($permission)) {
            $permission = $permissionClass->findByName($permission, $this->getDefaultGuardName());
        }

        if (is_int($permission)) {
            $permission = $permissionClass->findById($permission, $this->getDefaultGuardName());
        }

        if (! $this->getGuardNames()->contains($permission->guard_name)) {
            throw GuardDoesNotMatch::create($permission->guard_name, $this->getGuardNames());
        }

        return $this->permissions->contains('id', $permission->id);
    }
}
