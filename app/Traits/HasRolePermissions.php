<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait HasRolePermissions
{
    public function hasPermission(string $name): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        return in_array($name, static::permissionNamesFor($this->role), true);
    }

    public static function permissionNamesFor(?string $role): array
    {
        if (! $role) {
            return [];
        }

        return Cache::rememberForever("role_permissions.{$role}", function () use ($role) {
            return DB::table('role_permissions')
                ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                ->where('role_permissions.role', $role)
                ->pluck('permissions.name')
                ->all();
        });
    }
}