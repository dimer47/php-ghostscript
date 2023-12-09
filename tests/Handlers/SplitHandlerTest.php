<?php

namespace Tests\Handlers;

use Ordinary9843\Configs\Config;
use Ordinary9843\Handlers\SplitHandler;
use Ordinary9843\Constants\MessageConstant;
use Tests\BaseTest;

class SplitHandlerTest extends BaseTest
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testExecuteWithExistFileShouldSucceed(): void
    {
        $splitHandler = new SplitHandler();
        $splitHandler->getConfig()->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertCount(3, $splitHandler->execute(dirname(__DIR__, 2) . '/files/test.pdf', '/tmp/mock/files'));
        $this->assertEmpty($splitHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testExecuteWithNotExistFileShouldReturnErrorMessage(): void
    {
        $splitHandler = $this->getMockBuilder(SplitHandler::class)
            ->onlyMethods(['getPdfTotalPage', 'getConfig'])
            ->getMock();
        $splitHandler->method('getConfig')->willReturn(new Config(['binPath' => $this->getEnv('GS_BIN_PATH')]));
        $splitHandler->method('getPdfTotalPage')->willReturn(0);
        $this->assertCount(0, $splitHandler->execute(dirname(__DIR__, 2) . '/files/test.pdf', '/tmp/mock/files'));
        $this->assertNotEmpty($splitHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testExecuteFailedShouldReturnErrorMessage(): void
    {
        $splitHandler = new SplitHandler(new Config([
            'binPath' => $this->getEnv('GS_BIN_PATH')
        ]));
        $splitHandler->setOptions([
            'test' => true
        ]);
        $splitHandler->execute(dirname(__DIR__, 2) . '/files/test.pdf', '/tmp/mock/files');
        $this->assertNotEmpty($splitHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }
}
