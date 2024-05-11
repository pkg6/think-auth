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

//class MissingAbilityException extends AuthorizationException
use think\helper\Arr;

class MissingAbilityException extends \RuntimeException
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
     * @param  string|null  $message
     * @param  mixed  $code
     * @param  \Throwable|null  $previous
     *
     * @return void
     */
    public function __construct($abilities = [], $message = 'Invalid ability provided.')
    {
        parent::__construct($message ?? 'This action is unauthorized.', 0, $previous);

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
