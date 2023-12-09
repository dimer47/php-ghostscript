<?php

namespace Tests\Handlers;

use Exception;
use Ordinary9843\Configs\Config;
use Ordinary9843\Handlers\MergeHandler;
use Ordinary9843\Constants\MessageConstant;
use Tests\BaseTest;

class MergeHandlerTest extends BaseTest
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
     * @throws Exception
     */
    public function testExecuteWithExistFileShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $mergeHandler = new MergeHandler();
        $mergeHandler->getConfig()->setBinPath($this->getEnv('GS_BIN_PATH'));
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
     * @throws Exception
     */
    public function testExecuteWithNotExistFileShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $mergeHandler = new MergeHandler();
        $mergeHandler->getConfig()->setBinPath($this->getEnv('GS_BIN_PATH'));
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
     * @throws Exception
     */
    public function testExecuteWithNotPdfShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $mergeHandler = $this->getMockBuilder(MergeHandler::class)
            ->onlyMethods(['isPdf', 'getConfig'])
            ->getMock();
        $mergeHandler->method('getConfig')->willReturn(new Config(['binPath' => $this->getEnv('GS_BIN_PATH')]));
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
     * @throws Exception
     */
    public function testExecuteFailedShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $mergeHandler = new MergeHandler(new Config([
            'binPath' => $this->getEnv('GS_BIN_PATH')
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
