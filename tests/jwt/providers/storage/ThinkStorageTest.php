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

namespace tp5er\think\auth\Test\jwt\providers\storage;

use think\App;
use think\cache\driver\File;
use tp5er\think\auth\jwt\providers\storage\Think;
use tp5er\think\auth\Test\jwt\AbstractTestCase;

class ThinkStorageTest extends AbstractTestCase
{
    /**
     * @var Think
     */
    protected $storage;

    public function setUp(): void
    {
        parent::setUp();

        $app = App::getInstance();
        $app->bind('cache', File::class);
        $this->storage = new Think($app);
    }
    /** @test */
    public function test()
    {
        $this->storage->add('foo', 'bar', 10);
        $this->assertSame('bar', $this->storage->get('foo'));
        $this->assertTrue($this->storage->destroy('foo'));

        $this->storage->flush();
    }
}
