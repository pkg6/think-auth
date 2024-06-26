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

namespace tp5er\think\auth\Test\auth;

use PHPUnit\Framework\TestCase;
use tp5er\think\auth\access\AuthorizationException;
use tp5er\think\auth\access\Response;

class AuthAccessResponseTest extends TestCase
{
    public function testAllowMethod()
    {
        $response = Response::allow('some message', 'some_code');

        $this->assertTrue($response->allowed());
        $this->assertFalse($response->denied());
        $this->assertSame('some message', $response->message());
        $this->assertSame('some_code', $response->code());
    }

    public function testDenyMethod()
    {
        $response = Response::deny('some message', 'some_code');

        $this->assertTrue($response->denied());
        $this->assertFalse($response->allowed());
        $this->assertSame('some message', $response->message());
        $this->assertSame('some_code', $response->code());
    }

    public function testDenyMethodWithNoMessageReturnsNull()
    {
        $response = Response::deny();

        $this->assertNull($response->message());
    }

    public function testAuthorizeMethodThrowsAuthorizationExceptionWhenResponseDenied()
    {
        $response = Response::deny('Some message.', 'some_code');

        try {
            $response->authorize();
        } catch (AuthorizationException $e) {
            $this->assertSame('Some message.', $e->getMessage());
            $this->assertSame('some_code', $e->getCode());
            $this->assertEquals($response, $e->response());
        }
    }

    public function testAuthorizeMethodThrowsAuthorizationExceptionWithDefaultMessage()
    {
        $response = Response::deny();

        try {
            $response->authorize();
        } catch (AuthorizationException $e) {
            $this->assertSame('This action is unauthorized.', $e->getMessage());
        }
    }

    public function testThrowIfNeededDoesntThrowAuthorizationExceptionWhenResponseAllowed()
    {
        $response = Response::allow('Some message.', 'some_code');

        $this->assertEquals($response, $response->authorize());
    }

    public function testCastingToStringReturnsMessage()
    {
        $response = new Response(true, 'some data');
        $this->assertSame('some data', (string) $response);

        $response = new Response(false, null);
        $this->assertSame('', (string) $response);
    }

    public function testResponseToArrayMethod()
    {
        $response = new Response(false, 'Not allowed.', 'some_code');

        $this->assertEquals([
            'allowed' => false,
            'message' => 'Not allowed.',
            'code' => 'some_code',
        ], $response->toArray());
    }
}
