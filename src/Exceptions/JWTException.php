<?php

namespace tp5er\think\auth\Exceptions;

use Exception;

/**
 * Class JWTException
 * @package tp5er\think\auth\Exceptions
 */
class JWTException extends Exception
{
    /**
     * {@inheritdoc}
     */
    protected $message = 'An error occurred';
}