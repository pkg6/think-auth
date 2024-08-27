<?php

namespace tp5er\think\auth\support;

class File
{
    /**
     * @param $path
     * @return \FilesystemIterator
     */
    public static function fileIterator($path)
    {
        return new \FilesystemIterator($path, \FilesystemIterator::KEY_AS_FILENAME);
    }
}