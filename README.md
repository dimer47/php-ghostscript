# Ghostscript

[![build](https://github.com/ordinary9843/ghostscript/actions/workflows/build.yml/badge.svg)](https://github.com/ordinary9843/ghostscript/actions/workflows/build.yml)
[![codecov](https://codecov.io/gh/ordinary9843/ghostscript/branch/master/graph/badge.svg?token=DMXRZFN55V)](https://codecov.io/gh/ordinary9843/ghostscript)

### Intro

Use ghostscript to merge all PDF files or guess and convert PDF file version. Fix FPDF error by ghostscript: This document PDF probably uses a compression technique which is not supported by the free parser shipped with FPDI.

## Requirements

This library has the following requirements:

- PHP 7.1+
- Ghostscript 9+

## Installation

Requires:

```bash
apt-get install ghostscript
```

Require the package via composer:

```bash
composer require ordinary9843/ghostscript
```

## Usage

Example usage:

```php
<?php
require './vendor/autoload.php';

use Ordinary9843\Ghostscript;

$binPath = '/usr/bin/gs';
$tmpPath = '/tmp';
$ghostscript = new Ghostscript($binPath, $tmpPath);
$file = './files/test.pdf';

/**
 * Guess the pdf version.
 *
 * Output: '1.5'
 */
$ghostscript->guess($file);

/**
 * Convert the pdf version.
 *
 * Output: '1.4'
 */
$ghostscript->convert($file, Ghostscript::STABLE_VERSION);

/**
 * Merge all pdf.
 *
 * Output: './files/merge.pdf'
 */
$ghostscript->merge('./files/merge.pdf', [
    './files/part_1.pdf',
    './files/part_2.pdf',
    './files/part_3.pdf'
]);

/**
 * Clear temporary file.
 */
$ghostscript->clearTmpFile();

/**
 * Get all messages.
 *
 * Output: [
 *  '[INFO] Message.',
 *  '[ERROR] Message.'
 * ]
 */
$ghostscript->getMessages();
```

## Testing

```bash
composer test
```

## Licenses

(The [MIT](http://www.opensource.org/licenses/mit-license.php) License)

Copyright &copy; [Jerry Chen](https://ordinary9843.medium.com/)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE
