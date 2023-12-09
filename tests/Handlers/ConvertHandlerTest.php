<?php

namespace Tests\Handlers;

use PHPUnit\Framework\MockObject\Exception;
use Ordinary9843\Configs\Config;
use Ordinary9843\Cores\FileSystem;
use Ordinary9843\Handlers\ConvertHandler;
use Ordinary9843\Constants\MessageConstant;
use Tests\BaseTest;

class ConvertHandlerTest extends BaseTest
{
    /**
     * @return void
     * @throws \Exception
     */
    public function testExecuteWithExistFileShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $convertHandler = new ConvertHandler();
        $convertHandler->getConfig()->setBinPath($this->getEnv('GS_BIN_PATH'));
        $convertHandler->execute($file, 1.5);
        $this->assertFileExists($file);
        $this->assertEmpty($convertHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     * @throws Exception
     * @throws \Exception
     */
    public function testExecuteWithNotExistFileShouldReturnErrorMessage(): void
    {
        $fileSystem = $this->createMock(FileSystem::class);
        $fileSystem->method('isFile')->willReturn(false);
        $fileSystem->method('isValid')->willReturn(true);
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $convertHandler = new ConvertHandler(new Config([
            'binPath' => $this->getEnv('GS_BIN_PATH'),
            'fileSystem' => $fileSystem
        ]));
        $convertHandler->execute($file, 1.5);
        $this->assertFileExists($file);
        $this->assertNotEmpty($convertHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testExecuteWithNotPdfShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $convertHandler = $this->getMockBuilder(ConvertHandler::class)
            ->onlyMethods(['isPdf', 'getConfig'])
            ->getMock();
        $convertHandler->method('getConfig')->willReturn(new Config(['binPath' => $this->getEnv('GS_BIN_PATH')]));
        $convertHandler->method('isPdf')->willReturn(false);
        $convertHandler->execute($file, 1.5);
        $this->assertFileExists($file);
        $this->assertNotEmpty($convertHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testExecuteFailedShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $convertHandler = new ConvertHandler(new Config([
            'binPath' => $this->getEnv('GS_BIN_PATH')
        ]));
        $convertHandler->setOptions([
            'test' => true
        ]);
        $convertHandler->execute($file, 1.5);
        $this->assertFileExists($file);
        $this->assertNotEmpty($convertHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }
}
