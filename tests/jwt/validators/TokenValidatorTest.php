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

namespace tp5er\think\auth\Test\jwt\validators;

use tp5er\think\auth\jwt\validators\TokenValidator;
use tp5er\think\auth\Test\jwt\AbstractTestCase;

class TokenValidatorTest extends AbstractTestCase
{
    /**
     * @var TokenValidator
     */
    protected $validator;

    public function setUp()
    {
        parent::setUp();

        $this->validator = new TokenValidator;
    }

    /** @test */
    public function it_should_return_true_when_providing_a_well_formed_token()
    {
        $this->assertTrue($this->validator->isValid('one.two.three'));
    }

    public function dataProviderMalformedTokens()
    {
        return [
            ['one.two.'],
            ['.two.'],
            ['.two.three'],
            ['one..three'],
            ['..'],
            [' . . '],
            [' one . two . three '],
        ];
    }

    /**
     * @test
     * @dataProvider dataProviderMalformedTokens
     *
     * @param  string  $token
     */
    public function it_should_return_false_when_providing_a_malformed_token($token)
    {
        $this->assertFalse($this->validator->isValid($token));
    }

    /**
     * @test
     * @dataProvider dataProviderMalformedTokens
     *
     * @param  string  $token
     * @expectedException \tp5er\think\auth\jwt\exceptions\TokenInvalidException
     * @expectedExceptionMessage Malformed token
     */
    public function it_should_throw_an_exception_when_providing_a_malformed_token($token)
    {
        $this->validator->check($token);
    }

    public function dataProviderTokensWithWrongSegmentsNumber()
    {
        return [
            ['one.two'],
            ['one.two.three.four'],
            ['one.two.three.four.five'],
        ];
    }

    /**
     * @test
     * @dataProvider dataProviderTokensWithWrongSegmentsNumber
     *
     * @param  string  $token
     */
    public function it_should_return_false_when_providing_a_token_with_wrong_segments_number($token)
    {
        $this->assertFalse($this->validator->isValid($token));
    }

    /**
     * @test
     * @dataProvider dataProviderTokensWithWrongSegmentsNumber
     *
     * @param  string  $token
     * @expectedException \tp5er\think\auth\jwt\exceptions\TokenInvalidException
     * @expectedExceptionMessage Wrong number of segments
     */
    public function it_should_throw_an_exception_when_providing_a_malformed_token_with_wrong_segments_number($token)
    {
        $this->validator->check($token);
    }
}
