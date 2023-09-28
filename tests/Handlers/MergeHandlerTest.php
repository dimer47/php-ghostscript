<?php

namespace Tests\Handlers;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Configs\Config;
use Ordinary9843\Cores\FileSystem;
use Ordinary9843\Handlers\MergeHandler;
use Ordinary9843\Constants\MessageConstant;

class MergeHandlerTest extends TestCase
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
        $mergeHandler = new MergeHandler();
        $mergeHandler->getConfig()->setBinPath('/usr/bin/gs');
        $mergeHandler->execute($file, [
            dirname(__DIR__, 2) . '/files/part_1.pdf',
            dirname(__DIR__, 2) . '/files/part_2.pdf',
            dirname(__DIR__, 2) . '/files/part_3.pdf'
        ]);
        $this->assertFileExists($file);
        $this->assertEmpty($mergeHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     */
    public function testExecuteWithNotExistFileShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $mergeHandler = new MergeHandler();
        $mergeHandler->getConfig()->setBinPath('/usr/bin/gs');
        $mergeHandler->execute($file, [
            dirname(__DIR__, 2) . '/files/part_1.pdf',
            dirname(__DIR__, 2) . '/files/part_2.pdf',
            dirname(__DIR__, 2) . '/files/part_3.pdf',
            dirname(__DIR__, 2) . '/files/part_4.pdf'
        ]);
        $this->assertFileExists($file);
        $this->assertNotEmpty($mergeHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     */
    public function testExecuteWithNotPdfShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $mergeHandler = $this->getMockBuilder(MergeHandler::class)
            ->setConstructorArgs([new Config(['binPath' => '/usr/bin/gs'])])
            ->setMethods(['isPdf'])
            ->getMock();
        $mergeHandler->method('isPdf')->willReturn(false);
        $mergeHandler->execute($file, [
            dirname(__DIR__, 2) . '/files/part_1.pdf',
            dirname(__DIR__, 2) . '/files/part_2.pdf',
            dirname(__DIR__, 2) . '/files/part_3.pdf'
        ]);
        $this->assertFileExists($file);
        $this->assertNotEmpty($mergeHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     */
    public function testExecuteConvertFailedShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $mergeHandler = new MergeHandler(new Config([
            'binPath' => '/usr/bin/gs'
        ]));
        $mergeHandler->setOptions([
            'test' => true
        ]);
        $mergeHandler->execute($file, [
            dirname(__DIR__, 2) . '/files/part_1.pdf',
            dirname(__DIR__, 2) . '/files/part_2.pdf',
            dirname(__DIR__, 2) . '/files/part_3.pdf'
        ]);
        $this->assertFileExists($file);
        $this->assertNotEmpty($mergeHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }
}
