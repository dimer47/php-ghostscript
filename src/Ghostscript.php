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
    const MERGE_CONVERT = '%s -sDEVICE=pdfwrite -dNOPAUSE -dQUIET -dBATCH -sOUTPUTFILE=%s %s';

    /** @var float */
    const STABLE_VERSION = 1.4;

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
        $this->error = '[ERROR] ' . $error . PHP_EOL;
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
        $command = sprintf(self::CONVERT_CONVERT, $this->binPath, $version, $tmpFile, $file);
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
     * @param array $files
     * 
     * @return string
     */
    private function getMergeCommand(string $file, array $files): string
    {
        $command = sprintf(self::MERGE_CONVERT, $this->binPath, $file, implode(' ', $files));
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
        } elseif (!$this->isPdf($file)) {
            $this->setError($file . ' is not pdf.');

            return $file;
        }

        $tmpFile = $this->generateTmpFile();
        $command = $this->getConvertCommand($file, $newVersion, $tmpFile);
        $output = shell_exec($command);
        if ($output) {
            $this->setError('Failed to convert ' . $file . '. Because ' . $output);

            return $file;
        }

        copy($tmpFile, $file);

        return $file;
    }

    /**
     * @param string $file
     * @param float $newVersion
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
                $this->setError('Failed to convert, ' . $value . ' is not exist.');
                continue;
            } elseif (!$this->isPdf($value)) {
                unset($files[$key]);
                $this->setError($value . ' is not pdf.');
                continue;
            }

            if ($isAutoConvert === true) {
                if ($this->guess($value) !== self::STABLE_VERSION) {
                    $value = $this->convert($value, self::STABLE_VERSION);
                }
            }

            $files[$key] = $value;
        }

        $command = $this->getMergeCommand($file, $files);
        $output = shell_exec($command);
        if ($output) {
            $this->setError('Failed to convert ' . $file . '. Because ' . $output);

            return $file;
        }

        return $file;
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
