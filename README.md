# Ghostscript

[![build](https://github.com/ordinary9843/ghostscript/actions/workflows/build.yml/badge.svg)](https://github.com/ordinary9843/ghostscript/actions/workflows/build.yml)
[![codecov](https://codecov.io/gh/ordinary9843/ghostscript/branch/master/graph/badge.svg?token=DMXRZFN55V)](https://codecov.io/gh/ordinary9843/ghostscript)

## Intro

Use Ghostscript to merge / split all PDF files or guess and convert PDF file version. Fix FPDF error by Ghostscript: This document PDF probably uses a compression technique which is not supported by the free parser shipped with FPDI.

## Cores

This library has the following features:

- Full-featured: This library supports PDF version prediction, conversion, merger, and splitting.
- Lower dependency on external libraries: Most Ghostscript libraries have too high a dependency on other libraries.
- Compatible with multiple PHP versions: It can run properly on PHP 7.1, 7.2, 7.3, 7.4, and 8.0.

## Requirements

This library has the following requirements:

- PHP 7.1+
- Ghostscript 9.50+

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

$file = './files/test.pdf';
$binPath = '/usr/bin/gs';
$tmpPath = sys_get_temp_dir();
$ghostscript = new Ghostscript($binPath, $tmpPath);

/**
 * Set the binary path for PDF processing in Ghostscript.
 */
$ghostscript->setBinPath($binPath);

/**
 * Set the temporary file path for PDF processing in Ghostscript.
 */
$ghostscript->setTmpPath($tmpPath);

/**
 * Guess the PDF version.
 *
 * Output: 1.5
 */
$ghostscript->guess($file);

/**
 * Convert the PDF version.
 *
 * Output: './files/merge.pdf'
 */
$ghostscript->convert($file, 1.4);

/**
 * Merge all PDF.
 *
 * Output: './files/merge.pdf'
 */
$ghostscript->merge('./files/merge.pdf', [
    './files/part_1.pdf',
    './files/part_2.pdf',
    './files/part_3.pdf'
]);

/**
 * Split all PDF.
 *
 * Output: [
 *   './files/parts/part_1.pdf',
 *   './files/parts/part_2.pdf',
 *   './files/parts/part_3.pdf'
 * ]
 */
$ghostscript->split('./files/merge.pdf', './files/parts');

/**
 * Get all execution messages.
 *
 * Output: [
 *  '[INFO] Message.',
 *  '[ERROR] Message.'
 * ]
 */
$ghostscript->getMessages();

/**
 * Clear temporary files generated during the PDF processing.
 */
$ghostscript->clearTmpFiles();
```

## Testing

```bash
composer test
```

## Licenses

(The [MIT](http://www.opensource.org/licenses/mit-license.php) License)

Copyright &copy; [Jerry Chen](https://www.linkedin.com/in/ordinary9843/)

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
