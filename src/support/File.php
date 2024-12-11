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

    /**
     * @param $sourcePath
     * @param $targetPath
     *
     * @return \Generator
     */
    public static function publishes($sourcePath, $targetPath)
    {
        if ( ! file_exists($targetPath)) {
            mkdir($targetPath, 0775, true);
        }
        $fileIterator = File::fileIterator($sourcePath);
        /**
         * @var \SplFileInfo $file
         */
        foreach ($fileIterator as $file) {
            if ($file->isFile()) {
                $sourceFile = $file->getRealPath();
                $targetFile = $targetPath . DIRECTORY_SEPARATOR . $file->getBasename();
                if ( ! file_exists($targetFile)) {
                    $put = file_put_contents($targetFile, file_get_contents($sourceFile));
                } else {
                    $put = false;
                }
                yield [$put,$sourceFile,$targetFile];
            }
        }
    }
}
