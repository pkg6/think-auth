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

namespace tp5er\think\auth\Test\sanctum;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use tp5er\think\auth\sanctum\HasApiTokens;
use tp5er\think\auth\sanctum\PersonalAccessToken;

class HasApiTokensTest extends TestCase
{
    public function test_tokens_can_be_created()
    {
        $class = new ClassThatHasApiTokens;
        $time = Carbon::now();

        $newToken = $class->createToken('test', ['foo'], $time);

        [$id, $token] = explode('|', $newToken->plainTextToken);

        $this->assertEquals(
            $newToken->accessToken->token,
            hash('sha256', $token)
        );

        $this->assertEquals(
            $newToken->accessToken->id,
            $id
        );
    }
}
class ClassThatHasApiTokens implements \tp5er\think\auth\sanctum\contracts\HasApiTokens
{
    use HasApiTokens;

    public function tokens()
    {
        return new class {
            public function save(array $attributes)
            {
                return new PersonalAccessToken($attributes);
            }
        };
    }
}
