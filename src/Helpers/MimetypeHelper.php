<?php

namespace Ordinary9843\Helpers;

class MimetypeHelper
{
    /**
     * @param string $file
     * 
     * @return bool
     */
    public static function isPdf(string $file): bool
    {
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'pdf') {
            return false;
        }

        return (finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file) === 'application/pdf');
    }
}
