<?php

namespace tp5er\think\auth\Test\jwt;

use Carbon\Carbon;
use Mockery;
use PHPUnit\Framework\TestCase;

class AbstractTestCase extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Carbon::setTestNow($now = Carbon::now());
        $this->testNowTimestamp = $now->getTimestamp();
    }

    public function tearDown()
    {
        Carbon::setTestNow();
        Mockery::close();

        parent::tearDown();
    }
}
