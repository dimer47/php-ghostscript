<?php

namespace Ordinary9843\Handlers;

use Exception;
use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Constants\MessageConstant;
use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Constants\GhostscriptConstant;

class SplitHandler extends Handler implements HandlerInterface
{
    /**
     * @param array ...$arguments
     * 
     * @return array
     * 
     * @throws Exception
     */
    public function execute(...$arguments): array
    {
        $this->getConfig()->validateBinPath();

        try {
            $file = PathHelper::convertPathSeparator($arguments[0] ?? '');
            $path = $arguments[1] ?? '';
            $totalPage = $this->getPdfTotalPage($file);
            if ($totalPage < 1) {
                throw new Exception('Failed to read the total number of pages in ' . $file . '.');
            }

            (!$this->getConfig()->getFileSystem()->isDir($path)) && mkdir($path, 0755);
            $output = shell_exec($this->optionsToCommand(sprintf(GhostscriptConstant::SPLIT_COMMAND, $this->getConfig()->getBinPath(), 1, $totalPage, PathHelper::convertPathSeparator($path . GhostscriptConstant::SPLIT_FILENAME), $file)));
            if ($output) {
                throw new Exception('Failed to merge ' . $file . ', because ' . $output);
            }

            return array_map(function ($i) use ($path) {
                return $path . sprintf(GhostscriptConstant::SPLIT_FILENAME, $i);
            }, range(0, $totalPage - 1));
        } catch (Exception $e) {
            $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, $e->getMessage());

            return [];
        }
    }
}
