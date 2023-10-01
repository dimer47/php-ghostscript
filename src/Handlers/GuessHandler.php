<?php

namespace Ordinary9843\Handlers;

use Exception;
use Ordinary9843\Constants\MessageConstant;
use Ordinary9843\Interfaces\HandlerInterface;

class GuessHandler extends Handler implements HandlerInterface
{
    /**
     * @param array ...$arguments
     * 
     * @return float
     */
    public function execute(...$arguments): float
    {
        try {
            $file = $arguments[0] ?? '';
            if (!$this->getConfig()->getFileSystem()->isFile($file)) {
                throw new Exception('Failed to convert, ' . $file . ' is not exist.');
            }

            $fo = @fopen($file, 'rb');
            fseek($fo, 0);
            preg_match('/%PDF-(\d\.\d)/', fread($fo, 1024), $match);
            fclose($fo);

            return (float)($match[1] ?? 0);
        } catch (Exception $e) {
            $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, $e->getMessage());

            return 0;
        }
    }
}
