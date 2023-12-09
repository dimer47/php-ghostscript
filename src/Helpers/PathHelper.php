<?php

namespace Ordinary9843\Helpers;

class PathHelper
{
    /**
     * @param string $path
     *
     * @return string
     */
    public static function convertPathSeparator(string $path): string
    {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        if (strpos($path, ' ') !== false) {
            $path = escapeshellarg($path);
        }

        return $path;
    }
}
