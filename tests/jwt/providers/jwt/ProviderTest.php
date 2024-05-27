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

namespace tp5er\think\auth\Test\jwt\providers\jwt;

use tp5er\think\auth\jwt\providers\jwt\Provider;
use tp5er\think\auth\Test\jwt\AbstractTestCase;

class ProviderTest extends AbstractTestCase
{
    /**
     * @var JWTProviderStub
     */
    protected $provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->provider = new JWTProviderStub('secret', 'HS256', []);
    }

    /** @test */
    public function it_should_set_the_algo()
    {
        $this->provider->setAlgo('HS512');

        $this->assertSame('HS512', $this->provider->getAlgo());
    }

    /** @test */
    public function it_should_set_the_secret()
    {
        $this->provider->setSecret('foo');

        $this->assertSame('foo', $this->provider->getSecret());
    }
}
class JWTProviderStub extends Provider
{
    /**
     * {@inheritdoc}
     */
    protected function isAsymmetric()
    {
        return false;
    }
}
