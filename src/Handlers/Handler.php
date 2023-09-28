<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Configs\Config;
use Ordinary9843\Cores\FileSystem;
use Ordinary9843\Traits\MessageTrait;
use Ordinary9843\Constants\GhostscriptConstant;

class Handler
{
    use MessageTrait;

    /** @var Config */
    private static $config = null;

    /** @var FileSystem */
    private static $fileSystem = null;

    /** @var array */
    private static $options = [];

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        self::$config = $config;
        self::$fileSystem = new FileSystem();
    }

    /**
     * @param Config $config
     * 
     * @return void
     */
    public function setConfig(Config $config): void
    {
        self::$config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return self::$config;
    }

    /**
     * @param FileSystem $fileSystem
     * 
     * @return void
     */
    public function setFileSystem(FileSystem $fileSystem): void
    {
        self::$fileSystem = $fileSystem;
    }

    /**
     * @return FileSystem
     */
    public function getFileSystem(): FileSystem
    {
        return self::$fileSystem;
    }

    /**
     * @param array $options
     * 
     * @return void
     */
    public function setOptions(array $options): void
    {
        self::$options = $options;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return self::$options;
    }

    /**
     * @return string
     */
    public function getTmpFile(): string
    {
        return $this->getConfig()->getTmpPath() . DIRECTORY_SEPARATOR . uniqid(GhostscriptConstant::TMP_FILE_PREFIX) . '.pdf';
    }

    /**
     * @return int
     */
    public function getTmpFileCount(): int
    {
        $tmpPath = $this->getConfig()->getTmpPath();
        $files = scandir($tmpPath);
        $count = 0;
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $path = $tmpPath . DIRECTORY_SEPARATOR . $file;
            if ($this->getFileSystem()->isFile($path)) {
                $pathInfo = pathinfo($path);
                $filename = $pathInfo['filename'];
                (preg_match('/' . GhostscriptConstant::TMP_FILE_PREFIX . '/', $filename)) && $count++;
            }
        }

        return $count;
    }

    /**
     * @param bool $isForceClear
     * @param int $days
     * 
     * @return void
     */
    public function clearTmpFile(bool $isForceClear = false, int $days = 7): void
    {
        $deleteSeconds = $days * 86400;
        $tmpPath = $this->getConfig()->getTmpPath();
        $files = scandir($tmpPath);
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $path = $tmpPath . DIRECTORY_SEPARATOR . $file;
            if ($this->getFileSystem()->isFile($path)) {
                $createdAt = filemtime($path);
                $isExpired = time() - $createdAt > $deleteSeconds;
                if ($isForceClear === true || $isExpired === true) {
                    $pathInfo = pathinfo($path);
                    $filename = $pathInfo['filename'];
                    (preg_match('/' . GhostscriptConstant::TMP_FILE_PREFIX . '/', $filename)) && unlink($path);
                }
            }
        }
    }

    /**
     * @param string $command
     * 
     * @return string
     */
    public function optionsToCommand(string $command): string
    {
        $options = $this->getOptions();

        return (!empty($options)) ? $command .= ' ' . implode(' ', array_map(function ($key, $value) {
            return is_numeric($key) ? $value : $key . '=' . $value;
        }, array_keys($options), $options)) : $command;
    }
}
