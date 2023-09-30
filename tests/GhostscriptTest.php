<?php

namespace Tests;

use Exception;
use Ordinary9843\Ghostscript;
use PHPUnit\Framework\TestCase;

class GhostscriptTest extends TestCase
{
    /**
     * @return void
     */
    public function testConvertWithExistFileShouldSucceed(): void
    {
        $this->assertIsString((new Ghostscript('/usr/bin/gs'))->convert(dirname(__DIR__, 2) . '/files/test.pdf', 1.5));
    }

    /**
     * @return void
     */
    public function testGuessWithExistFileShouldSucceed(): void
    {
        $this->assertIsFloat((new Ghostscript('/usr/bin/gs'))->guess(dirname(__DIR__, 2) . '/files/test.pdf'));
    }

    /**
     * @return void
     */
    public function testMergeWithExistFilesShouldSucceed(): void
    {
        $this->assertIsString((new Ghostscript('/usr/bin/gs'))->merge(dirname(__DIR__, 2) . '/files/test.pdf', [
            dirname(__DIR__, 2) . '/files/part_1.pdf',
            dirname(__DIR__, 2) . '/files/part_2.pdf',
            dirname(__DIR__, 2) . '/files/part_3.pdf'
        ]));
    }

    /**
     * @return void
     */
    public function testSetBinPathShouldEqualGetBinPath(): void
    {
        $ghostscript = new Ghostscript();
        $binPath = '/usr/bin/gs';
        $ghostscript->setBinPath($binPath);
        $this->assertEquals($binPath, $ghostscript->getBinPath());
    }

    /**
     * @return void
     */
    public function testSetTmpPathShouldEqualGetTmpPath(): void
    {
        $ghostscript = new Ghostscript();
        $tmpPath = sys_get_temp_dir();
        $ghostscript->setTmpPath($tmpPath);
        $this->assertEquals($tmpPath, $ghostscript->getTmpPath());
    }

    /**
     * @return void
     */
    public function testSetOptionsShouldEqualGetOptions(): void
    {
        $ghostscript = new Ghostscript();
        $options = ['-dSAFER'];
        $ghostscript->setOptions($options);
        $this->assertEquals($options, $ghostscript->getOptions());
    }

    /**
     * @return void
     */
    public function testGetMessagesShouldReturnArray(): void
    {
        $this->assertIsArray((new Ghostscript())->getMessages());
    }

    /**
     * @return void
     */
    public function testInvalidMethodShouldThrowException(): void
    {
        $this->expectException(Exception::class);
        (new Ghostscript())->test();
    }
}
