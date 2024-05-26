<?php

namespace tp5er\think\auth\Test\jwt\validators;

use tp5er\think\auth\jwt\claims\Collection;
use tp5er\think\auth\jwt\claims\Expiration;
use tp5er\think\auth\jwt\claims\IssuedAt;
use tp5er\think\auth\jwt\claims\Issuer;
use tp5er\think\auth\jwt\claims\JwtId;
use tp5er\think\auth\jwt\claims\NotBefore;
use tp5er\think\auth\jwt\claims\Subject;
use tp5er\think\auth\jwt\validators\PayloadValidator;
use tp5er\think\auth\Test\jwt\AbstractTestCase;

class PayloadValidatorTest extends AbstractTestCase
{
    /**
     * @var PayloadValidator
     */
    protected $validator;

    public function setUp()
    {
        parent::setUp();

        $this->validator = new PayloadValidator;
    }

    /** @test */
    public function it_should_return_true_when_providing_a_valid_payload()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp + 3600),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp),
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $this->assertTrue($this->validator->isValid($collection));
    }

    /**
     * @test
     * @expectedException \tp5er\think\auth\jwt\exceptions\TokenExpiredException
     * @expectedExceptionMessage Token has expired
     */
    public function it_should_throw_an_exception_when_providing_an_expired_payload()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp - 1440),
            new NotBefore($this->testNowTimestamp - 3660),
            new IssuedAt($this->testNowTimestamp - 3660),
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $this->validator->check($collection);
    }

    /**
     * @test
     * @expectedException \tp5er\think\auth\jwt\exceptions\TokenInvalidException
     * @expectedExceptionMessage Not Before (nbf) timestamp cannot be in the future
     */
    public function it_should_throw_an_exception_when_providing_an_invalid_nbf_claim()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp + 1440),
            new NotBefore($this->testNowTimestamp + 3660),
            new IssuedAt($this->testNowTimestamp - 3660),
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $this->validator->check($collection);
    }

    /**
     * @test
     * @expectedException \tp5er\think\auth\jwt\exceptions\InvalidClaimException
     * @expectedExceptionMessage Invalid value provided for claim [iat]
     */
    public function it_should_throw_an_exception_when_providing_an_invalid_iat_claim()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp + 1440),
            new NotBefore($this->testNowTimestamp - 3660),
            new IssuedAt($this->testNowTimestamp + 3660),
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $this->validator->check($collection);
    }

    /**
     * @test
     * @expectedException \tp5er\think\auth\jwt\exceptions\TokenInvalidException
     * @expectedExceptionMessage JWT payload does not contain the required claims
     */
    public function it_should_throw_an_exception_when_providing_an_invalid_payload()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
        ];

        $collection = Collection::make($claims);

        $this->validator->check($collection);
    }

    /**
     * @test
     * @expectedException \tp5er\think\auth\jwt\exceptions\InvalidClaimException
     * @expectedExceptionMessage Invalid value provided for claim [exp]
     */
    public function it_should_throw_an_exception_when_providing_an_invalid_expiry()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration('foo'),
            new NotBefore($this->testNowTimestamp - 3660),
            new IssuedAt($this->testNowTimestamp + 3660),
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $this->validator->check($collection);
    }

    /** @test */
    public function it_should_set_the_required_claims()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
        ];

        $collection = Collection::make($claims);

        $this->assertTrue($this->validator->setRequiredClaims(['iss', 'sub'])->isValid($collection));
    }

    /** @test */
    public function it_should_check_the_token_in_the_refresh_context()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp - 1000),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp - 2600), // this is LESS than the refresh ttl at 1 hour
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $this->assertTrue(
            $this->validator->setRefreshFlow()->setRefreshTTL(60)->isValid($collection)
        );
    }

    /** @test */
    public function it_should_return_true_if_the_refresh_ttl_is_null()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp - 1000),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp - 2600), // this is LESS than the refresh ttl at 1 hour
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $this->assertTrue(
            $this->validator->setRefreshFlow()->setRefreshTTL(null)->isValid($collection)
        );
    }

    /**
     * @test
     * @expectedException \tp5er\think\auth\jwt\exceptions\TokenExpiredException
     * @expectedExceptionMessage Token has expired and can no longer be refreshed
     */
    public function it_should_throw_an_exception_if_the_token_cannot_be_refreshed()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp - 5000), // this is MORE than the refresh ttl at 1 hour, so is invalid
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $this->validator->setRefreshFlow()->setRefreshTTL(60)->check($collection);
    }
}

