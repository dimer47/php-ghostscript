<?php

namespace Tests\Handlers;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Handlers\GuessHandler;
use Ordinary9843\Constants\MessageConstant;

class GuessHandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function testExecuteWithExistFileShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $guessHandler = new GuessHandler();
        $guessHandler->getConfig()->setBinPath('/usr/bin/gs');
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
        $guessHandler->getConfig()->setBinPath('/usr/bin/gs');
        $this->assertEquals(0.0, $guessHandler->execute($file));
        $this->assertFileNotExists($file);
        $this->assertNotEmpty($guessHandler->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }
}
