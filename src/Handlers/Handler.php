<?php

namespace Ordinary9843\Handlers;

use Exception;
use Ordinary9843\Configs\Config;
use Ordinary9843\Traits\MessageTrait;
use Ordinary9843\Constants\MessageConstant;
use Ordinary9843\Constants\GhostscriptConstant;

class Handler
{
    use MessageTrait;

    /** @var Config */
    private static $config = null;

    /** @var array */
    private $options = [];

    /**
     * @param Config $config
     */
    public function __construct(Config $config = null)
    {
        self::$config = ($config !== null) ? $config : new Config();
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
     * @param array $options
     *
     * @return void
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
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
            if ($this->getConfig()->getFileSystem()->isFile($path)) {
                $pathInfo = pathinfo($path);
                $filename = $pathInfo['filename'];
                (preg_match('/' . GhostscriptConstant::TMP_FILE_PREFIX . '/', $filename)) && $count++;
            }
        }

        return $count;
    }

    /**
     * @param bool $isForceClear
     * @param int  $days
     *
     * @return void
     */
    public function clearTmpFiles(bool $isForceClear = false, int $days = 7): void
    {
        $deleteSeconds = $days * 86400;
        $tmpPath = $this->getConfig()->getTmpPath();
        $files = scandir($tmpPath);
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $path = $tmpPath . DIRECTORY_SEPARATOR . $file;
            if ($this->getConfig()->getFileSystem()->isFile($path)) {
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

    /**
     * @param string $file
     *
     * @return int
     */
    public function getPdfTotalPage(string $file): int
    {
        try {
            $this->getConfig()->validateBinPath();

            if (!$this->getConfig()->getFileSystem()->isFile($file)) {
                throw new Exception($file . ' is not exist.');
            } elseif (!$this->isPdf($file)) {
                throw new Exception($file . ' is not PDF.');
            }

            $output = shell_exec(
                sprintf(
                    GhostscriptConstant::TOTAL_PAGE_COMMAND,
                    $this->getConfig()->getBinPath(),
                    $file
                )
            );

            return ($output) ? (int)$output : 0;
        } catch (Exception $e) {
            $this->addMessage(MessageConstant::MESSAGE_TYPE_ERROR, $e->getMessage());

            return 0;
        }
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    public function isPdf(string $file): bool
    {
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'pdf') {
            return false;
        }

        return (mime_content_type($file) === 'application/pdf');
    }
}
