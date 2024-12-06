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

use FilesystemIterator;

class Str extends \think\helper\Str
{
    public static function filesystemIteratorHGName(FilesystemIterator $filesystemIterator, $name)
    {
        /* @var \SplFileInfo $file */
        foreach ($filesystemIterator as $file) {
            if ($file->isFile()) {
                if (Str::endsWith($file->getBasename(), $name)) {
                    return $file;
                }
            }
        }

        return false;
    }

    /**
     * Make a string's first character uppercase.
     *
     * @param string $string
     *
     * @return string
     */
    public static function ucfirst($string)
    {
        return static::upper(static::substr($string, 0, 1)) . static::substr($string, 1);
    }

    /**
     * Parse a Class[@]method style callback into class and method.
     *
     * @param string $callback
     * @param string|null $default
     *
     * @return array<int, string|null>
     */
    public static function parseCallback($callback, $default = null)
    {
        return static::contains($callback, '@') ? explode('@', $callback, 2) : [$callback, $default];
    }
}
