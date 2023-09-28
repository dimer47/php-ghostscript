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
        return (!$path && !is_dir($path) && !is_file($path));
    }
}
