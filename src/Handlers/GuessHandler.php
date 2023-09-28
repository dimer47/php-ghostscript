<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Cores\FileSystem;
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
        $file = $arguments[0] ?? '';
        $version = 0;
        if (!$this->getConfig()->getFileSystem()->isFile($file)) {
            $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, $file . ' is not exist.');

            return $version;
        }

        $fo = @fopen($file, 'rb');
        fseek($fo, 0);
        preg_match('/%PDF-(\d\.\d)/', fread($fo, 1024), $match);
        fclose($fo);

        return (float)($match[1] ?? $version);
    }
}
