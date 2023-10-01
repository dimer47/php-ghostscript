<?php

namespace Ordinary9843\Constants;

class GhostscriptConstant
{
    /** @var string */
    const TMP_FILE_PREFIX = 'ghostscript_tmp_file_';

    /** @var string */
    const CONVERT_COMMAND = '%s -sDEVICE=pdfwrite -dNOPAUSE -dQUIET -dBATCH -dCompatibilityLevel=%s -sOutputFile=%s %s';

    /** @var string */
    const MERGE_COMMAND = '%s -sDEVICE=pdfwrite -dNOPAUSE -dQUIET -dBATCH -sOUTPUTFILE=%s %s';

    /** @var string */
    const SPLIT_COMMAND = '%s -sDEVICE=pdfwrite -dNOPAUSE -dQUIET -dBATCH -sOUTPUTFILE=%s %s';

    /** @var float */
    const STABLE_VERSION = 1.4;
}
