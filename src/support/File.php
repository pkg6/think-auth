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

class File
{
    /**
     * @param $path
     *
     * @return \FilesystemIterator
     */
    public static function fileIterator($path)
    {
        return new \FilesystemIterator($path, \FilesystemIterator::KEY_AS_FILENAME);
    }
}
