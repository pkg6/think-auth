<?php

namespace tp5er\think\auth\permission\exceptions;

use InvalidArgumentException;
use tp5er\think\auth\support\Collection;

class GuardDoesNotMatch extends InvalidArgumentException
{
    public static function create(string $givenGuard, Collection $expectedGuards)
    {
        return new static("The given role or permission should use guard `{$expectedGuards->implode(', ')}` instead of `{$givenGuard}`.");
    }
}