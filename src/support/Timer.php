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

namespace tp5er\think\auth\support;

use DateInterval;
use DateTime;
use DateTimeInterface;

class Timer
{

    /**
     * @param $timestamp
     *
     * @return false|int
     */
    public static function timestamp($timestamp)
    {
        if ($timestamp instanceof DateTimeInterface) {
            return $timestamp->getTimestamp();
        }
        if (is_string($timestamp)) {
            if ($timestamp = strtotime($timestamp)) {
                return $timestamp;
            }
        }

        return (int) $timestamp;
    }

    /**
     * @param $sec
     *
     * @return int
     *
     * @throws \Exception
     */
    public static function timeAddSec($sec = null)
    {
        $dateTime = new DateTime();
        if ( ! is_null($sec)) {
            $dateTime->add(new DateInterval(sprintf("PT%sS", $sec)));
        }

        return $dateTime->getTimestamp();
    }
}
