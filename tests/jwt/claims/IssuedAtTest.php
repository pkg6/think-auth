<?php

namespace tp5er\think\auth\Test\jwt\claims;

use tp5er\think\auth\jwt\claims\IssuedAt;
use tp5er\think\auth\Test\jwt\AbstractTestCase;

class IssuedAtTest extends AbstractTestCase
{

    /**
     * @test
     * @expectedException \tp5er\think\auth\jwt\exceptions\InvalidClaimException
     * @expectedExceptionMessage Invalid value provided for claim [iat]
     */
    public function it_should_throw_an_exception_when_passing_a_future_timestamp()
    {
        new IssuedAt($this->testNowTimestamp + 3600);
    }
}
