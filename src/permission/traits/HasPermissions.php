<?php

namespace tp5er\think\auth\permission\traits;

use tp5er\think\auth\permission\Guard;
use tp5er\think\auth\permission\PermissionRegistrar;
use tp5er\think\auth\support\Collection;

trait HasPermissions
{
    protected $permissionClass;

    public function getPermissionClass()
    {
        if (! isset($this->permissionClass)) {
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
}