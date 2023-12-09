<?php

namespace Ordinary9843\Cores;

class FileSystem
{
    /**
     * @param string $path
     *
     * @return bool
     */
    public function isValid(string $path): bool
    {
        return ($path && ($this->isDir($path) || $this->isFile($path)));
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function isDir(string $path): bool
    {
        return ($path && is_dir($path));
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function isFile(string $path): bool
    {
        return ($path && is_file($path));
    }
}
