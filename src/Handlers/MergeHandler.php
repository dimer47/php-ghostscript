<?php

namespace Ordinary9843\Handlers;

use Exception;
use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Handlers\GuessHandler;
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

        try {
            $file = PathHelper::convertPathSeparator($arguments[0] ?? '');
            $files = $arguments[1] ?? [];
            $isAutoConvert = (bool)($arguments[2] ?? true);
            $files = array_filter($files, function ($value) use ($isAutoConvert) {
                $value = PathHelper::convertPathSeparator($value);
                if (!$this->getConfig()->getFileSystem()->isFile($value)) {
                    $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, $value . ' is not exist.');

                    return false;
                } elseif (!$this->isPdf($value)) {
                    $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, $value . ' is not PDF.');

                    return false;
                }
                ($isAutoConvert === true && $this->guessHandler->execute($value) !== GhostscriptConstant::STABLE_VERSION) && $value = $this->convertHandler->execute($value, GhostscriptConstant::STABLE_VERSION);

                return true;
            });

            $output = shell_exec($this->optionsToCommand(sprintf(GhostscriptConstant::MERGE_COMMAND, $this->getConfig()->getBinPath(), $file, implode(' ', $files))));
            if ($output) {
                throw new Exception('Failed to merge ' . $file . ', because ' . $output);
            }

            return $file;
        } catch (Exception $e) {
            $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, $e->getMessage());

            return '';
        }
    }
}
