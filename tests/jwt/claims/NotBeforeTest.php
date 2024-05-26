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

namespace tp5er\think\auth\Test\jwt\claims;

use tp5er\think\auth\jwt\claims\NotBefore;
use tp5er\think\auth\Test\jwt\AbstractTestCase;

class NotBeforeTest extends AbstractTestCase
{
    /**
     * @test
     * @expectedException \tp5er\think\auth\jwt\exceptions\InvalidClaimException
     * @expectedExceptionMessage Invalid value provided for claim [nbf]
     */
    public function it_should_throw_an_exception_when_passing_an_invalid_value()
    {
        new NotBefore('foo');
    }
}
