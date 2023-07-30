<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Constants\MessageConstant;
use Ordinary9843\Traits\MessageTrait;

class MessageTraitTest extends TestCase
{
    use MessageTrait;

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
    public function testShouldArrayHasKeyWhenGetMessages(): void
    {
        $messages = $this->getMessages();
        $this->assertArrayHasKey(MessageConstant::MESSAGE_TYPE_INFO, $messages);
        $this->assertArrayHasKey(MessageConstant::MESSAGE_TYPE_ERROR, $messages);
    }

    /**
     * @return void
     */
    public function testShouldNotEmptyInfoMessageWhenGetMessages(): void
    {
        $this->addMessage(MessageConstant::MESSAGE_TYPE_INFO, 'Message.');
        $this->assertNotEmpty($this->getMessages()[MessageConstant::MESSAGE_TYPE_INFO]);
    }

    /**
     * @return void
     */
    public function testShouldNotEmptyErrorMessageWhenGetMessages(): void
    {
        $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, 'Message.');
        $this->assertNotEmpty($this->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }
}
