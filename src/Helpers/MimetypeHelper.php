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
