<?php

namespace Tests\Handlers;

use Ordinary9843\Handlers\GuessHandler;
use Ordinary9843\Constants\MessageConstant;
use Tests\BaseTest;

class GuessHandlerTest extends BaseTest
{
    /**
     * @return void
     */
    public function testExecuteWithExistFileShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $guessHandler = new GuessHandler();
        $guessHandler->getConfig()->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertEquals(1.5, $guessHandler->execute($file));
        $this->assertFileExists($file);
        $this->assertEmpty($guessHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     */
    public function testExecuteWithNotExistFileShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/part_4.pdf';
        $guessHandler = new GuessHandler();
        $guessHandler->getConfig()->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertEquals(0.0, $guessHandler->execute($file));
        $this->assertFileDoesNotExist($file);
        $this->assertNotEmpty($guessHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }
}
