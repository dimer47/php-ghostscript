<?php

namespace Ordinary9843\Handlers;

use Exception;
use Ordinary9843\Helpers\PathHelper;
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
        $this->getConfig()->validateBinPath();

        try {
            $file = PathHelper::convertPathSeparator($arguments[0] ?? '');
            $version = $arguments[1] ?? 0;
            if (!$this->getConfig()->getFileSystem()->isFile($file)) {
                throw new Exception('Failed to convert, ' . $file . ' is not exist.');
            } elseif (!$this->isPdf($file)) {
                throw new Exception($file . ' is not PDF.');
            }

            $tmpFile = $this->getTmpFile();
            $output = shell_exec($this->optionsToCommand(sprintf(GhostscriptConstant::CONVERT_COMMAND, $this->getConfig()->getBinPath(), $version, $tmpFile, $file)));
            if ($output) {
                throw new Exception('Failed to convert ' . $file . ', because ' . $output);
            }

            copy($tmpFile, $file);

            return $file;
        } catch (Exception $e) {
            $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, $e->getMessage());

            return '';
        }
    }
}
