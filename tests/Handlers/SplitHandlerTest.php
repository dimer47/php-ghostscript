<?php

namespace Tests\Handlers;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Configs\Config;
use Ordinary9843\Handlers\SplitHandler;
use Ordinary9843\Constants\MessageConstant;

class SplitHandlerTest extends TestCase
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
     */
    public function testExecuteWithExistFileShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $splitHandler = new SplitHandler();
        $splitHandler->getConfig()->setBinPath('/usr/bin/gs');
        $this->assertCount(3, $splitHandler->execute($file, dirname(__DIR__, 2) . '/files/split'));
        $this->assertEmpty($splitHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     */
    public function testExecuteWithNotExistFileShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $splitHandler = $this->getMockBuilder(SplitHandler::class)
            ->setConstructorArgs([new Config(['binPath' => '/usr/bin/gs'])])
            ->setMethods(['getPdfTotalPage'])
            ->getMock();
        $splitHandler->method('getPdfTotalPage')->willReturn(0);
        $this->assertCount(0, $splitHandler->execute($file, dirname(__DIR__, 2) . '/files/split'));
        $this->assertNotEmpty($splitHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     */
    public function testExecuteFailedShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $splitHandler = new SplitHandler(new Config([
            'binPath' => '/usr/bin/gs'
        ]));
        $splitHandler->setOptions([
            'test' => true
        ]);
        $splitHandler->execute($file, dirname(__DIR__, 2) . '/files/split');
        $this->assertFileExists($file);
        $this->assertNotEmpty($splitHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }
}
