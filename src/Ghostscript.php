<?php

namespace Ordinary9843;

use Ordinary9843\Constants\GhostscriptConstant;
use Ordinary9843\Constants\MessageConstant;
use Ordinary9843\Traits\GhostscriptTrait;
use Ordinary9843\Traits\MessageTrait;
use Exception;

class Ghostscript
{
    use GhostscriptTrait, MessageTrait;

    /**
     * @param string $binPath
     * @param string $tmpPath
     */
    public function __construct(string $binPath = '', string $tmpPath = '')
    {
        ($this->getBinPath() === '') && ($binPath !== '') && $this->setBinPath($binPath);
        ($this->getTmpPath() === '') && ($tmpPath !== '') ? $this->setTmpPath($tmpPath) : $this->setTmpPath(sys_get_temp_dir());
    }

    /**
     * @param string $file
     * @param float $version
     * @param string $tmpFile
     * 
     * @return string
     */
    private function getConvertCommand(string $file, float $version, string $tmpFile): string
    {
        return $this->optionsToCommand(sprintf(GhostscriptConstant::CONVERT_COMMAND, $this->binPath, $version, $tmpFile, $file));
    }

    /**
     * @param string $file
     * @param array $files
     * 
     * @return string
     */
    private function getMergeCommand(string $file, array $files): string
    {
        return $this->optionsToCommand(sprintf(GhostscriptConstant::MERGE_COMMAND, $this->binPath, $file, implode(' ', $files)));
    }

    /**
     * @param string $file
     * 
     * @return float
     */
    public function guess(string $file): float
    {
        $version = 0;
        if (!is_file($file)) {
            $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, $file . ' is not exist.');

            return $version;
        }

        $fo = @fopen($file, 'rb');
        fseek($fo, 0);
        preg_match('/%PDF-(\d\.\d)/', fread($fo, 1024), $match);
        fclose($fo);
        $version = (float)($match[1] ?? $version);

        return $version;
    }

    /**
     * @param string $file
     * @param float $newVersion
     * 
     * @return string
     * 
     * @throws Exception
     */
    public function convert(string $file, float $newVersion): string
    {
        $this->validateBinPath();
        $file = $this->convertPathSeparator($file);
        if (!is_file($file)) {
            $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, 'Failed to convert, ' . $file . ' is not exist.');

            return $file;
        } elseif (!$this->isPdf($file)) {
            $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, $file . ' is not pdf.');

            return $file;
        }

        $tmpFile = $this->getTmpFile();
        $command = $this->getConvertCommand($file, $newVersion, $tmpFile);
        $output = shell_exec($command);
        if ($output) {
            $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, 'Failed to convert ' . $file . '. Because ' . $output);

            return $file;
        }

        copy($tmpFile, $file);

        return $file;
    }

    /**
     * @param string $file
     * @param array $files
     * @param bool $isAutoConvert
     * 
     * @return string
     * 
     * @throws Exception
     */
    public function merge(string $file, array $files, bool $isAutoConvert = true): string
    {
        $this->validateBinPath();

        foreach ($files as $key => $value) {
            $value = $this->convertPathSeparator($value);
            if (!is_file($value)) {
                unset($files[$key]);
                $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, 'Failed to convert, ' . $value . ' is not exist.');
                continue;
            } elseif (!$this->isPdf($value)) {
                unset($files[$key]);
                $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, $value . ' is not pdf.');
                continue;
            }

            if ($isAutoConvert === true) {
                ($this->guess($value) !== GhostscriptConstant::STABLE_VERSION) && $value = $this->convert($value, GhostscriptConstant::STABLE_VERSION);
            }

            $files[$key] = $value;
        }

        $command = $this->getMergeCommand($file, $files);
        $output = shell_exec($command);
        if ($output) {
            $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, 'Failed to convert ' . $file . '. Because ' . $output);

            return $file;
        }

        return $file;
    }
}
