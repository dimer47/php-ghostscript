<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Cores\FileSystem;
use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Handlers\GuessHandler;
use Ordinary9843\Helpers\MimetypeHelper;
use Ordinary9843\Handlers\ConvertHandler;
use Ordinary9843\Constants\MessageConstant;
use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Constants\GhostscriptConstant;

class MergeHandler extends Handler implements HandlerInterface
{
    /** @var ConvertHandler */
    private $convertHandler = null;

    /** @var GuessHandler */
    private $guessHandler = null;

    public function __construct()
    {
        $this->convertHandler = new ConvertHandler($this->getConfig());
        $this->guessHandler = new GuessHandler($this->getConfig());
    }

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

        $file = $arguments[0] ?? '';
        $files = $arguments[1] ?? [];
        $isAutoConvert = $arguments[2] ?? true;
        foreach ($files as $key => $value) {
            $value = PathHelper::convertPathSeparator($value);
            if (!$this->getFileSystem()->($value)) {
                unset($files[$key]);
                $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, 'Failed to convert, ' . $value . ' is not exist.');
                continue;
            } elseif (!MimetypeHelper::isPdf($value)) {
                unset($files[$key]);
                $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, $value . ' is not PDF.');
                continue;
            }

            ($isAutoConvert === true) && ($this->guessHandler->execute($value) !== GhostscriptConstant::STABLE_VERSION) && $value = $this->convertHandler->execute($value, GhostscriptConstant::STABLE_VERSION);
            $files[$key] = $value;
        }

        $output = shell_exec($this->optionsToCommand(sprintf(GhostscriptConstant::MERGE_COMMAND, $this->getConfig()->getBinPath(), $file, implode(' ', $files))));
        if ($output) {
            $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, 'Failed to convert ' . $file . ', because ' . $output);

            return '';
        }

        return $file;
    }
}
