<?php

namespace Ordinary9843\Cores;

class FileSystem
{
    /**
     * @param string $path
     * 
     * @return string
     */
    public function isValid(string $path): bool
    {
        return (!empty($path) && ($this->isDir($path) || $this->isFile($path)));
    }

    /**
     * @param string $path
     * 
     * @return string
     */
    public function isDir(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * @param string $path
     * 
     * @return string
     */
    public function isFile(string $path): bool
    {
        return is_file($path);
    }
}
