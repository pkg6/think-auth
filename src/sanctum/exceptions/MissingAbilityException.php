<?php

/*
 * This file is part of the tp5er/think-auth
 *
 * (c) pkg6 <https://github.com/pkg6>
 *
 * (L) Licensed <https://opensource.org/license/MIT>
 *
 * (A) zhiqiang <https://www.zhiqiang.wang>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace tp5er\think\auth\sanctum\exceptions;

use think\helper\Arr;
use tp5er\think\auth\access\AuthorizationException;

class MissingAbilityException extends AuthorizationException
{

    /**
     * The abilities that the user did not have.
     *
     * @var array
     */
    protected $abilities;

    /**
     * Create a new authorization exception instance.
     *
     * @param array $abilities
     * @param string|null $message
     */
    public function __construct($abilities = [], $message = 'Invalid ability provided.')
    {
        parent::__construct($message ?? 'This action is unauthorized.', 0);

        $this->abilities = Arr::wrap($abilities);
    }
    /**
     * Get the abilities that the user did not have.
     *
     * @return array
     */
    public function abilities()
    {
        return $this->abilities;
    }

}
