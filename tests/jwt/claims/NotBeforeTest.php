<?php

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
