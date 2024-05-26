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

namespace tp5er\think\auth\jwt;

use tp5er\think\auth\jwt\claims\Collection;
use tp5er\think\auth\jwt\contracts\JWT as JWTContract;
use tp5er\think\auth\jwt\exceptions\JWTException;
use tp5er\think\auth\jwt\exceptions\TokenBlacklistedException;
use tp5er\think\auth\jwt\support\CustomClaims;
use tp5er\think\auth\jwt\support\RefreshFlow;

class Manager
{
    use CustomClaims, RefreshFlow;

    /**
     * The provider.
     *
     * @var JWTContract
     */
    protected $provider;

    /**
     * The blacklist.
     *
     * @var Blacklist
     */
    protected $blacklist;

    /**
     * the payload factory.
     *
     * @var ClaimsValidatorFactory
     */
    protected $payloadFactory;

    /**
     * The blacklist flag.
     *
     * @var bool
     */
    protected $blacklistEnabled = true;

    /**
     * the persistent claims.
     *
     * @var array
     */
    protected $persistentClaims = [];

    /**
     * Constructor.
     *
     * @param contracts\JWT $provider
     * @param Blacklist $blacklist
     * @param ClaimsValidatorFactory $payloadFactory
     *
     * @return void
     */
    public function __construct(JWTContract $provider, Blacklist $blacklist, ClaimsValidatorFactory $payloadFactory)
    {
        $this->provider = $provider;
        $this->blacklist = $blacklist;
        $this->payloadFactory = $payloadFactory;
    }

    /**
     * Encode a Payload and return the Token.
     *
     * @param Payload $payload
     *
     * @return Token
     */
    public function encode(Payload $payload)
    {
        $token = $this->provider->encode($payload->get());

        return new Token($token);
    }

    /**
     * Decode a Token and return the Payload.
     *
     * @param Token $token
     * @param bool $checkBlacklist
     *
     * @return Payload
     *
     * @throws Exceptions\TokenBlacklistedException
     */
    public function decode(Token $token, $checkBlacklist = true)
    {
        $payloadArray = $this->provider->decode($token->get());

        $payload = $this->payloadFactory
            ->setRefreshFlow($this->refreshFlow)
            ->customClaims($payloadArray)
            ->make();

        if ($checkBlacklist && $this->blacklistEnabled && $this->blacklist->has($payload)) {
            throw new TokenBlacklistedException('The token has been blacklisted');
        }

        return $payload;
    }

    /**
     * Refresh a Token and return a new Token.
     *
     * @param Token $token
     * @param bool $forceForever
     * @param bool $resetClaims
     *
     * @return Token
     */
    public function refresh(Token $token, $forceForever = false, $resetClaims = false)
    {
        $this->setRefreshFlow();

        $claims = $this->buildRefreshClaims($this->decode($token));

        if ($this->blacklistEnabled) {
            // Invalidate old token
            $this->invalidate($token, $forceForever);
        }

        // Return the new token
        return $this->encode(
            $this->payloadFactory->customClaims($claims)->make($resetClaims)
        );
    }

    /**
     * Invalidate a Token by adding it to the blacklist.
     *
     * @param Token $token
     * @param bool $forceForever
     *
     * @return bool
     *
     * @throws Exceptions\JWTException
     */
    public function invalidate(Token $token, $forceForever = false)
    {
        if ( ! $this->blacklistEnabled) {
            throw new JWTException('You must have the blacklist enabled to invalidate a token.');
        }

        return call_user_func(
            [$this->blacklist, $forceForever ? 'addForever' : 'add'],
            $this->decode($token, false)
        );
    }

    /**
     * Build the claims to go into the refreshed token.
     *
     * @param Payload $payload
     *
     * @return array
     */
    protected function buildRefreshClaims(Payload $payload)
    {
        // Get the claims to be persisted from the payload
        $persistentClaims = Collection::make($payload->toArray())
            ->only($this->persistentClaims)
            ->toArray();

        // persist the relevant claims
        return array_merge(
            $this->customClaims,
            $persistentClaims,
            [
                'sub' => $payload['sub'],
                'iat' => $payload['iat'],
            ]
        );
    }

    /**
     * Get the Payload Factory instance.
     *
     * @return ClaimsValidatorFactory
     */
    public function getPayloadFactory()
    {
        return $this->payloadFactory;
    }

    /**
     * Get the JWTProvider instance.
     *
     * @return JWTContract
     */
    public function getJWTProvider()
    {
        return $this->provider;
    }

    /**
     * Get the Blacklist instance.
     *
     * @return Blacklist
     */
    public function getBlacklist()
    {
        return $this->blacklist;
    }

    /**
     * Set whether the blacklist is enabled.
     *
     * @param bool $enabled
     *
     * @return $this
     */
    public function setBlacklistEnabled($enabled)
    {
        $this->blacklistEnabled = $enabled;

        return $this;
    }

    /**
     * Set the claims to be persisted when refreshing a token.
     *
     * @param array $claims
     *
     * @return $this
     */
    public function setPersistentClaims(array $claims)
    {
        $this->persistentClaims = $claims;

        return $this;
    }
}
