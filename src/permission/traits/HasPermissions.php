<?php

namespace tp5er\think\auth\permission\traits;

use tp5er\think\auth\permission\contracts\Permission;
use tp5er\think\auth\permission\exceptions\GuardDoesNotMatch;
use tp5er\think\auth\permission\exceptions\PermissionDoesNotExist;
use tp5er\think\auth\permission\Guard;
use tp5er\think\auth\permission\PermissionRegistrar;
use tp5er\think\auth\support\Collection;

trait HasPermissions
{
    protected $permissionClass;

    public function getPermissionClass()
    {
        if (!isset($this->permissionClass)) {
            $this->permissionClass = app(PermissionRegistrar::class)->getPermissionClass();
        }

        return $this->permissionClass;
    }

    protected function getDefaultGuardName(): string
    {
        return Guard::getDefaultName($this);
    }

    protected function getGuardNames(): Collection
    {
        return Guard::getNames($this);
    }

    public function givePermissionTo(...$permissions)
    {
        $permissions = Collection::make($permissions)
            ->flatten()
            ->map(function ($permission) {
                if (empty($permission)) {
                    return false;
                }
                return $this->getStoredPermission($permission);
            })
            ->filter(function ($permission) {
                return $permission instanceof Permission;
            })
            ->each(function ($permission) {
                $this->ensureModelSharesGuard($permission);
            })->pluck('id')->all();
        $model = $this->getModel();
        if (!$model->isEmpty()) {
            $this->permissions()->sync($permissions, false);
        }
        $this->forgetCachedPermissions();
        return $this;
    }

    public function syncPermissions(...$permissions)
    {
        $this->permissions()->detach();
        return $this->givePermissionTo($permissions);
    }

    public function revokePermissionTo($permission)
    {
        $this->permissions()->detach($this->getStoredPermission($permission));
        $this->forgetCachedPermissions();
        return $this;
    }

    public function getPermissionNames(): Collection
    {
        return Collection::make($this->permissions)->pluck('name');
    }

    public function hasAllDirectPermissions(...$permissions): bool
    {
        $permissions = Collection::make($permissions)->flatten();
        foreach ($permissions as $permission) {
            if (!$this->hasDirectPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    public function hasDirectPermission($permission): bool
    {
        $permissionClass = $this->getPermissionClass();

        if (is_string($permission)) {
            $permission = $permissionClass->findByName($permission, $this->getDefaultGuardName());
        }
        if (is_int($permission)) {
            $permission = $permissionClass->findById($permission, $this->getDefaultGuardName());
        }
        if (!$permission instanceof Permission) {
            throw new PermissionDoesNotExist;
        }
        return Collection::make($this->permissions)->contains('id', $permission->id);
    }
    public function forgetCachedPermissions()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function getStoredPermission($permissions)
    {
        $permissionClass = $this->getPermissionClass();
        if (is_numeric($permissions)) {
            return $permissionClass->findById($permissions, $this->getDefaultGuardName());
        }
        if (is_string($permissions)) {
            return $permissionClass->findByName($permissions, $this->getDefaultGuardName());
        }
        if (is_array($permissions)) {
            return $permissionClass
                ->whereIn('name', $permissions)
                ->whereIn('guard_name', $this->getGuardNames())
                ->select();
        }
        return $permissions;
    }

    protected function ensureModelSharesGuard($roleOrPermission)
    {
        if (!$this->getGuardNames()->contains($roleOrPermission->guard_name)) {
            throw GuardDoesNotMatch::create($roleOrPermission->guard_name, $this->getGuardNames());
        }
    }
}