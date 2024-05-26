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

namespace tp5er\think\auth\exceptions;

class CredentialsException extends \RuntimeException
{
    const codeMissing = 1;
    const codeNoRecordFound = 2;

    protected $codeMessage = [
        CredentialsException::codeMissing => "Missing parameter or missing password field",
        CredentialsException::codeNoRecordFound => "No record found",
    ];

    public $code;
    public $tableOrModel;
    public $credentials;

    public function __construct($code, $tableOrModel, $credentials)
    {
        $this->code = $code;
        $this->tableOrModel = $tableOrModel;
        $this->credentials = $credentials;

        parent::__construct($this->codeMessage[$code] ?? "unknown error");
    }
}
