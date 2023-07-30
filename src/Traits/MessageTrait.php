<?php

namespace Ordinary9843\Traits;

use Ordinary9843\Constants\MessageConstant;

trait MessageTrait
{
    /** @var array */
    private $messages = [
        MessageConstant::MESSAGE_TYPE_INFO => [],
        MessageConstant::MESSAGE_TYPE_ERROR => []
    ];

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param string $type
     * @param string $message
     * 
     * @return void
     */
    public function addMessage(string $type, string $message): void
    {
        (!array_key_exists($type, $this->messages)) && $type = MessageConstant::MESSAGE_TYPE_INFO;
        (!in_array($message, $this->messages[$type])) && $this->messages[$type][] = date('Y-m-d H:i:s') . ' [' . $type . '] ' . $message;
    }
}
