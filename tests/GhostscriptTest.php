<?php

namespace Tests;

use Exception;
use Ordinary9843\Ghostscript;

class GhostscriptTest extends BaseTest
{
    /**
     * @return void
     */
    public function testConvertWithExistFileShouldSucceed(): void
    {
        $this->assertIsString((new Ghostscript($this->getEnv('GS_BIN_PATH')))->convert(dirname(__DIR__, 2) . '/files/test.pdf', 1.5));
    }

    /**
     * @return void
     */
    public function testGuessWithExistFileShouldSucceed(): void
    {
        $this->assertIsFloat((new Ghostscript($this->getEnv('GS_BIN_PATH')))->guess(dirname(__DIR__, 2) . '/files/test.pdf'));
    }

    /**
     * @return void
     */
    public function testMergeWithExistFilesShouldSucceed(): void
    {
        $this->assertIsString((new Ghostscript($this->getEnv('GS_BIN_PATH')))->merge(dirname(__DIR__, 2) . '/files/test.pdf', [
            dirname(__DIR__, 2) . '/files/part_1.pdf',
            dirname(__DIR__, 2) . '/files/part_2.pdf',
            dirname(__DIR__, 2) . '/files/part_3.pdf'
        ]));
    }

    /**
     * @return void
     */
    public function testSplitWithExistFilesShouldSucceed(): void
    {
        $this->assertIsArray((new Ghostscript($this->getEnv('GS_BIN_PATH')))->split(dirname(__DIR__, 2) . '/files/test.pdf', dirname(__DIR__, 2) . '/files/split'));
    }

    /**
     * @return void
     */
    public function testSetBinPathShouldEqualGetBinPath(): void
    {
        $ghostscript = new Ghostscript();
        $binPath = $this->getEnv('GS_BIN_PATH');
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
