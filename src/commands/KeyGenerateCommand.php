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

namespace tp5er\think\auth\commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use tp5er\think\auth\support\Str;

class KeyGenerateCommand extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        // 指令配置
        $this->setName('auth:key-generate')
            ->setDescription('Add jwt secret to the. env file');
    }

    /**
     * @param Input $input
     * @param Output $output
     *
     * @return int|void|null
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function execute(Input $input, Output $output)
    {

        $key      = Str::random(64);
        $filename = $this->app->getRootPath() . '.env';
        if (file_exists($filename) === false) {
            $this->displayKey($key, $output);
            return;
        }
        if (Str::contains(file_get_contents($filename), 'JWT_SECRET')) {
            $output->comment('Secret key already exists. Skipping...');
            return;
        }
        $textLineArray = [];
        //存在中括号的行数
        $bracketNumber = 0;
        $currentNumber = 0;
        // 打开文件
        $file = fopen($filename, 'r');
        // 逐行读取文件内容
        while (!feof($file)) {
            // 读取一行内容
            $line = fgets($file);

            if ($line === false) {
                break;
            }
            if (Str::startsWith($line, '[')) {
                if ($bracketNumber <= 0) {
                    $bracketNumber = $currentNumber;
                }
            };
            $textLineArray[$currentNumber] = $line;
            $currentNumber                 += 1;
        }
        // 关闭文件
        fclose($file);
        if ($bracketNumber > 0) {
            //在中括号之前添加
            $textLineArray[$bracketNumber - 1] .= $textLineArray[$bracketNumber - 1] . 'JWT_SECRET=' . $key . PHP_EOL . PHP_EOL;
        } else {
            //在末尾添加即可
            $textLineArray[] = PHP_EOL . 'JWT_SECRET=' . $key . PHP_EOL;
        }
        $text = implode('', $textLineArray);
        file_put_contents($filename, $text);
        $this->displayKey($key, $output);
    }

    /**
     * Display the key.
     *
     * @param string $key
     * @return void
     */
    protected function displayKey($key, Output $output)
    {
        $key = $this->app->config->get('auth.jwt.secret', $key);
        $output->info("jwt-auth secret [$key] set successfully.");
    }
}
