<?php

namespace Ordinary9843;

use Exception;

class Ghostscript
{
    /** @var string */
    const TMP_FILE_PREFIX = 'ghostscript_tmp_file_';

    /** @var string */
    const CONVERT_CONVERT = '%s -sDEVICE=pdfwrite -dNOPAUSE -dQUIET -dBATCH -dCompatibilityLevel=%s -sOutputFile=%s %s';

    /** @var string */
    protected $binPath = '';

    /** @var string */
    protected $tmpPath = '';

    /** @var array */
    protected $options = [];

    /** @var string */
    protected $error = '';

    /**
     * @param string $binPath
     * @param string $tmpPath
     */
    public function __construct(string $binPath = '', string $tmpPath = '')
    {
        if ($this->getBinPath() === '') {
            if ($binPath !== '') {
                $this->setBinPath($binPath);
            }
        }

        if ($this->getTmpPath() === '') {
            if ($tmpPath !== '') {
                $this->setTmpPath($tmpPath);
            } else {
                $this->setTmpPath(sys_get_temp_dir());
            }
        }
    }

    /**
     * @param string $path
     * 
     * @return string
     */
    protected function convertPathSeparator(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    /**
     * @return string
     */
    private function generateTmpFile(): string
    {
        return $this->getTmpPath() . DIRECTORY_SEPARATOR . uniqid(self::TMP_FILE_PREFIX) . '.pdf';
    }

    /**
     * @param bool $isForceDelete
     * @param int $days
     * 
     * @return void
     */
    public function deleteTmpFile(bool $isForceDelete = false, int $days = 7): void
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
                if ($isForceDelete === true || $isExpired === true) {
                    $pathInfo = pathinfo($path);
                    $filename = $pathInfo['filename'];
                    if (preg_match('/' . self::TMP_FILE_PREFIX . '/', $filename)) {
                        unlink($path);
                    }
                }
            }
        }
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
                (preg_match('/' . self::TMP_FILE_PREFIX . '/', $filename)) && $count++;
            }
        }

        return $count;
    }

    /**
     * @return void
     * 
     * @throws Exception
     */
    private function validateBinPath(): void
    {
        $binPath = $this->getBinPath();
        if (!is_dir($binPath) && !is_file($binPath)) {
            throw new Exception('The ghostscript binary path is not set.');
        }
    }

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
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @return void
     */
    protected function setError(string $error): void
    {
        $this->error = $error . PHP_EOL;
    }

    /**
     * @param float $version
     * @param string $tmpFile
     * @param string $file
     * 
     * @return string
     */
    private function getConvertCommand(float $version, string $tmpFile, string $file): string
    {
        $command = sprintf(self::CONVERT_CONVERT, $this->binPath, $version, $tmpFile, escapeshellarg($file));
        $options = $this->getOptions();
        if (!empty($options)) {
            foreach ($options as $key => $value) {
                if (!is_numeric($key)) {
                    $command .= ' ' . $key . '=' . $value;
                } else {
                    $command .= ' ' . $value;
                }
            }
        }

        return $command;
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
            $this->setError($file . ' is not exist.');

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
            $this->setError('Failed to convert, ' . $file . ' is not exist.');

            return $file;
        }

        $tmpFile = $this->generateTmpFile();
        $command = $this->getConvertCommand($newVersion, $tmpFile, $file);
        $output = shell_exec($command);
        if ($output) {
            $this->setError('Failed to convert ' . $file . '. Because ' . $output);

            return $file;
        }

        copy($tmpFile, $file);

        return $file;
    }
}
