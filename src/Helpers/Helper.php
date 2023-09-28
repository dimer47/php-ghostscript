<?php

namespace Ordinary9843\Helpers;

class Helper
{
    /**
     * @param string $path
     * 
     * @return string
     */
    public static function convertPathSeparator(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    /**
     * @param string $file
     * 
     * @return bool
     */
    public static function isPdf(string $file): bool
    {
        // TODO: optimize logic.
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
