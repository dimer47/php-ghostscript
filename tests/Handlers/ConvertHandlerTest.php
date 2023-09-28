<?php

namespace Tests\Handlers;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Configs\Config;
use Ordinary9843\Cores\FileSystem;
use Ordinary9843\Handlers\ConvertHandler;
use Ordinary9843\Constants\MessageConstant;

class ConvertHandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function testExecuteWithExistFileShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $convertHandler = new ConvertHandler();
        $convertHandler->getConfig()->setBinPath('/usr/bin/gs');
        $convertHandler->execute($file, 1.5);
        $this->assertFileExists($file);
        $this->assertEmpty($convertHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     */
    public function testExecuteWithNotExistFileShouldReturnErrorMessage(): void
    {
        $fileSystem = $this->createMock(FileSystem::class);
        $fileSystem->method('isFile')->willReturn(false);
        $fileSystem->method('isValid')->willReturn(true);
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $convertHandler = new ConvertHandler(new Config([
            'binPath' => '/usr/bin/gs',
            'fileSystem' => $fileSystem
        ]));
        $convertHandler->execute($file, 1.5);
        $this->assertFileExists($file);
        $this->assertNotEmpty($convertHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     */
    public function testExecuteWithNotPdfShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $convertHandler = $this->getMockBuilder(ConvertHandler::class)
            ->setConstructorArgs([new Config(['binPath' => '/usr/bin/gs'])])
            ->setMethods(['isPdf'])
            ->getMock();
        $convertHandler->method('isPdf')->willReturn(false);
        $convertHandler->execute($file, 1.5);
        $this->assertFileExists($file);
        $this->assertNotEmpty($convertHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     */
    public function testExecuteConvertFailedShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $convertHandler = new ConvertHandler(new Config([
            'binPath' => '/usr/bin/gs'
        ]));
        $convertHandler->setOptions([
            'test' => true
        ]);
        $convertHandler->execute($file, 1.5);
        $this->assertFileExists($file);
        $this->assertNotEmpty($convertHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }
}
