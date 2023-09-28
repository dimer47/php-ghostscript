<?php

namespace Ordinary9843\Configs;

use Exception;
use Ordinary9843\Cores\FileSystem;
use Ordinary9843\Helpers\PathHelper;

class Config
{
    /** @var string */
    private $binPath = '';

    /** @var string */
    private $tmpPath = '';

    /** @var FileSystem */
    private static $fileSystem = null;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $binPath = $config['binPath'] ?? '';
        $tmpPath = $config['tmpPath'] ?? '';
        ($this->getBinPath() === '') && ($binPath !== '') && $this->setBinPath($binPath);
        ($this->getTmpPath() === '') && ($tmpPath !== '') ? $this->setTmpPath($tmpPath) : $this->setTmpPath(sys_get_temp_dir());
        self::$fileSystem = $config['fileSystem'] ?? new FileSystem();
    }

    /**
     * @param string $binPath
     * 
     * @return void
     */
    public function setBinPath(string $binPath): void
    {
        $this->binPath = PathHelper::convertPathSeparator($binPath);
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
        $this->tmpPath = PathHelper::convertPathSeparator($tmpPath);
    }

    /**
     * @return string
     */
    public function getTmpPath(): string
    {
        return $this->tmpPath;
    }

    /**
     * @return void
     * 
     * @throws Exception
     */
    public function validateBinPath(): void
    {
        $binPath = $this->getBinPath();
        if (!self::$fileSystem->isValid($binPath)) {
            throw new Exception('The Ghostscript binary path is not set.');
        }
    }
}
