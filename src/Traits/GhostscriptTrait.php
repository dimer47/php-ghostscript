<?php

namespace Ordinary9843\Traits;

use Ordinary9843\Constants\GhostscriptConstant;
use Exception;

trait GhostscriptTrait
{
    /** @var string */
    protected $binPath = '';

    /** @var string */
    protected $tmpPath = '';

    /** @var array */
    protected $options = [];

    /**
     * @param string $binPath
     * 
     * @return void
     */
    public function setBinPath(string $binPath): void
    {
        $this->binPath = $this->convertPathSeparator($binPath);
    }

    /**
     * @return string
     */
    public function getBinPath(): string
    {
        return $this->binPath;
    }

    /**
     * @param string $tmpPath
     * 
     * @return void
     */
    public function setTmpPath(string $tmpPath): void
    {
        $this->tmpPath = $this->convertPathSeparator($tmpPath);
    }

    /**
     * @return string
     */
    public function getTmpPath(): string
    {
        return $this->tmpPath;
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
        return $this->getTmpPath() . DIRECTORY_SEPARATOR . uniqid(GhostscriptConstant::TMP_FILE_PREFIX) . '.pdf';
    }

    /**
     * @return int
     */
    public function getTmpFileCount(): int
    {
        $tmpPath = $this->getTmpPath();
        $files = scandir($tmpPath);
        $count = 0;
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $path = $tmpPath . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
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
        $tmpPath = $this->getTmpPath();
        $files = scandir($tmpPath);
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $path = $tmpPath . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
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
     * @param string $path
     * 
     * @return string
     */
    public function convertPathSeparator(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    /**
     * @return void
     * 
     * @throws Exception
     */
    public function validateBinPath(): void
    {
        $binPath = $this->getBinPath();
        if (!is_dir($binPath) && !is_file($binPath)) {
            throw new Exception('The ghostscript binary path is not set.');
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
        if (!empty($options)) {
            foreach ($options as $key => $value) {
                (!is_numeric($key)) ? $command .= ' ' . $key . '=' . $value : $command .= ' ' . $value;
            }
        }

        return $command;
    }

    /**
     * @param string $file
     * 
     * @return bool
     */
    public function isPdf(string $file): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (
            pathinfo($file, PATHINFO_EXTENSION) !== 'pdf' ||
            finfo_file($finfo, $file) !== 'application/pdf'
        ) {
            return false;
        }

        return true;
    }
}
