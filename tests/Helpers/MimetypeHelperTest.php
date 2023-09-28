<?php

namespace Tests\Helpers;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Helpers\MimetypeHelper;

class MimetypeHelperTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function testFileShouldValidPdf(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($file, '%PDF-');
        rename($file, $file .= '.pdf');
        $this->assertTrue(MimetypeHelper::isPdf($file));
        @unlink($file);
    }

    /**
     * @return void
     */
    public function testFileShouldNotValidPdf(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'txt');
        file_put_contents($file, 'txt');
        rename($file, $file .= '.txt');
        $this->assertFalse(MimetypeHelper::isPdf($file));
        @unlink($file);
    }
}
