<?php

namespace tp5er\think\auth\permission\traits;

use tp5er\think\auth\permission\contracts\Role;
use tp5er\think\auth\permission\PermissionRegistrar;
use tp5er\think\auth\support\Collection;

trait HasRoles
{
    use HasPermissions;

    protected $roleClass;

    public function getRoleClass()
    {
        if (!isset($this->roleClass)) {
            $this->roleClass = app(PermissionRegistrar::class)->getRoleClass();
        }

        return $this->roleClass;
    }

    public function assignRole(...$roles)
    {
        $roles = collect($roles)
            ->flatten()
            ->map(function ($role) {
                if (empty($role)) {
                    return false;
                }
                return $this->getStoredRole($role);
            })
            ->filter(function ($role) {
                return $role instanceof Role;
            })
            ->each(function ($role) {
                $this->ensureModelSharesGuard($role);
            })->pluck('id')->all();
        $model = $this->getModel();
        if (!$model->isEmpty()) {
            $this->roles()->sync($roles, false);
        }
        $this->forgetCachedPermissions();
        return $this;
    }

    public function removeRole($role)
    {
        $this->roles()->detach($this->getStoredRole($role));
        $this->forgetCachedPermissions();
        return $this;
    }

    public function syncRoles(...$roles)
    {
        $this->roles()->detach();
        return $this->assignRole($roles);
    }

    public function hasRole($roles, string $guard = null): bool
    {
        if (is_string($roles) && false !== strpos($roles, '|')) {
            $roles = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return $guard
                ? Collection::make($this->roles)->where('guard_name', $guard)->contains('name', $roles)
                : Collection::make($this->roles)->contains('name', $roles);
        }

        if (is_int($roles)) {
            return $guard
                ? Collection::make($this->roles)->where('guard_name', $guard)->contains('id', $roles)
                : Collection::make($this->roles)->contains('id', $roles);
        }

        if ($roles instanceof Role) {
            return Collection::make($this->roles)->contains('id', $roles->id);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role, $guard)) {
                    return true;
                }
            }

            return false;
        }
        return !$roles->intersect($guard ?
            Collection::make($this->roles)->where('guard_name', $guard) :
            $this->roles
        )->isEmpty();
    }

    protected function getStoredRole($role): Role
    {
        $roleClass = $this->getRoleClass();
        if (is_numeric($role)) {
            return $roleClass::findById($role, $this->getDefaultGuardName());
        }
        if (is_string($role)) {
            return $roleClass::findByName($role, $this->getDefaultGuardName());
        }
        return $role;
    }

    protected function convertPipeToArray(string $pipeString)
    {
        $pipeString = trim($pipeString);

        if (strlen($pipeString) <= 2) {
            return $pipeString;
        }

        $quoteCharacter = substr($pipeString, 0, 1);
        $endCharacter = substr($quoteCharacter, -1, 1);

        if ($quoteCharacter !== $endCharacter) {
            return explode('|', $pipeString);
        }

        if (!in_array($quoteCharacter, ["'", '"'])) {
            return explode('|', $pipeString);
        }

        return explode('|', trim($pipeString, $quoteCharacter));
    }
}