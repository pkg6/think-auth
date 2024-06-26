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

use tp5er\think\auth\jwt\contracts\Storage;
use tp5er\think\auth\support\Timer;

class Blacklist
{
    /**
     * The storage.
     *
     * @var Storage
     */
    protected $storage;

    /**
     * The grace period when a token is blacklisted. In seconds.
     *
     * @var int
     */
    protected $gracePeriod = 0;

    /**
     * Number of minutes from issue date in which a JWT can be refreshed.
     *
     * @var int
     */
    protected $refreshTTL = 20160;

    /**
     * The unique key held within the blacklist.
     *
     * @var string
     */
    protected $key = 'jti';

    /**
     * Constructor.
     *
     * @param  Storage  $storage
     *
     * @return void
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Add the token (jti claim) to the blacklist.
     *
     * @param  Payload  $payload
     *
     * @return bool
     */
    public function add(Payload $payload)
    {
        // if there is no exp claim then add the jwt to
        // the blacklist indefinitely
        if ( ! $payload->hasKey('exp')) {
            return $this->addForever($payload);
        }

        // if we have already added this token to the blacklist
        if ( ! empty($this->storage->get($this->getKey($payload)))) {
            return true;
        }

        $this->storage->add(
            $this->getKey($payload),
            ['valid_until' => $this->getGraceTimestamp()],
            $this->getMinutesUntilExpired($payload)
        );

        return true;
    }

    /**
     * Get the number of minutes until the token expiry.
     *
     * @param  Payload  $payload
     *
     * @return int
     */
    protected function getMinutesUntilExpired(Payload $payload)
    {
        $exp = Timer::timestamp($payload['exp']);
        $iat = Timer::timestamp($payload['iat']);

        // get the latter of the two expiration dates and find
        // the number of minutes until the expiration date,
        // plus 1 minute to avoid overlap
        return $exp->max($iat->addMinutes($this->refreshTTL))->addMinute()->diffInRealMinutes();
    }

    /**
     * Add the token (jti claim) to the blacklist indefinitely.
     *
     * @param  Payload  $payload
     *
     * @return bool
     */
    public function addForever(Payload $payload)
    {
        $this->storage->forever($this->getKey($payload), 'forever');

        return true;
    }

    /**
     * Determine whether the token has been blacklisted.
     *
     * @param  Payload  $payload
     *
     * @return bool
     */
    public function has(Payload $payload)
    {
        $val = $this->storage->get($this->getKey($payload));
        // exit early if the token was blacklisted forever,
        if ($val === 'forever') {
            return true;
        }

        // check whether the expiry + grace has past
        return ! empty($val) && ! Timer::isFuture($val['valid_until']);
    }

    /**
     * Remove the token (jti claim) from the blacklist.
     *
     * @param  Payload  $payload
     *
     * @return bool
     */
    public function remove(Payload $payload)
    {
        return $this->storage->destroy($this->getKey($payload));
    }

    /**
     * Remove all tokens from the blacklist.
     *
     * @return bool
     */
    public function clear()
    {
        $this->storage->flush();

        return true;
    }

    /**
     * Get the timestamp when the blacklist comes into effect
     * This defaults to immediate (0 seconds).
     *
     * @return int
     */
    protected function getGraceTimestamp()
    {
        return Timer::now()->addSeconds($this->gracePeriod)->getTimestamp();
    }

    /**
     * Set the grace period.
     *
     * @param  int  $gracePeriod
     *
     * @return $this
     */
    public function setGracePeriod($gracePeriod)
    {
        $this->gracePeriod = (int) $gracePeriod;

        return $this;
    }

    /**
     * Get the grace period.
     *
     * @return int
     */
    public function getGracePeriod()
    {
        return $this->gracePeriod;
    }

    /**
     * Get the unique key held within the blacklist.
     *
     * @param  Payload  $payload
     *
     * @return mixed
     */
    public function getKey(Payload $payload)
    {
        return $payload($this->key);
    }

    /**
     * Set the unique key held within the blacklist.
     *
     * @param  string  $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = value($key);

        return $this;
    }

    /**
     * Set the refresh time limit.
     *
     * @param  int  $ttl
     *
     * @return $this
     */
    public function setRefreshTTL($ttl)
    {
        $this->refreshTTL = (int) $ttl;

        return $this;
    }

    /**
     * Get the refresh time limit.
     *
     * @return int
     */
    public function getRefreshTTL()
    {
        return $this->refreshTTL;
    }
}
