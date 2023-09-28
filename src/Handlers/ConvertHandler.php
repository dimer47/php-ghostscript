<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Helpers\Helper;
use Ordinary9843\Constants\MessageConstant;
use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Constants\GhostscriptConstant;

class ConvertHandler extends Handler implements HandlerInterface
{
    /**
     * @param array ...$arguments
     * 
     * @return string
     * 
     * @throws Exception
     */
    public function execute(...$arguments): string
    {
        $file = $arguments[0] ?? '';
        $version = $arguments[1] ?? 0;
        $file = Helper::convertPathSeparator($file);
        if (!is_file($file)) {
            $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, 'Failed to convert, ' . $file . ' is not exist.');

            return '';
        } elseif (!Helper::isPdf($file)) {
            $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, $file . ' is not PDF.');

            return '';
        }

        $tmpFile = $this->getTmpFile();
        $output = shell_exec($this->optionsToCommand(sprintf(GhostscriptConstant::CONVERT_COMMAND, $this->getConfig()->getBinPath(), $version, $tmpFile, $file)));
        if ($output) {
            $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, 'Failed to convert ' . $file . ', because ' . $output);

            return '';
        }

        copy($tmpFile, $file);

        return $file;
    }
}
