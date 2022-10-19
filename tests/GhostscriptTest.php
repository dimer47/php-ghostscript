<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Ghostscript;

class GhostscriptTest extends TestCase
{
    /** @var float */
    const OLD_VERSION = 1.4;

    /** @var float */
    const NEW_VERSION = 1.5;

    /** @var string */
    protected $testFile = __DIR__ . '/../files/test.pdf';

    /** @var string */
    protected $fakeFile = __DIR__ . '/../files/fake.pdf';

    /** @var string */
    protected $binPath = '';

    /** @var string */
    protected $tmpPath = '';

    /**
     * This method is called before each test
     * 
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->binPath = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'C:\gs\gs9.55.0\bin\gswin64c.exe' : '/usr/bin/gs';
        $this->tmpPath = sys_get_temp_dir();
    }

    /**
     * Test Ghostscript binary absolute path
     * 
     * @return void
     */
    public function testBinPath(): void
    {
        $ghostscript = new Ghostscript($this->binPath);
        $binPath = $ghostscript->getBinPath();
        $this->assertEquals($binPath, $this->binPath);

        $output = shell_exec($this->binPath . ' --version');
        $version = floatval($output);
        $this->assertNotEquals($version, 0);
    }

    /**
     * Test temporary save file absolute path
     * 
     * @return void
     */
    public function testTmpPath(): void
    {
        $ghostscript = new Ghostscript();
        $tmpPath = $ghostscript->getTmpPath();
        $this->assertEquals($tmpPath, sys_get_temp_dir());

        $ghostscript = new Ghostscript($this->binPath, sys_get_temp_dir());
        $tmpPath = $ghostscript->getTmpPath();
        $this->assertEquals($tmpPath, sys_get_temp_dir());
    }

    /**
     * Test guess PDF version.
     * 
     * @return void
     */
    public function testGuess(): void
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $version = $ghostscript->guess($this->testFile);
        $this->assertContains($version, [
            self::OLD_VERSION,
            self::NEW_VERSION
        ]);

        $version = $ghostscript->guess($this->fakeFile);
        $error = $ghostscript->getError();
        $this->assertEquals($version, 0);
        $this->assertNotEquals($error, '');
    }

    /**
     * Test convert PDF version
     * 
     * @return void
     */
    public function testConvert(): void
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $ghostscript->convert($this->testFile, self::NEW_VERSION);
        $version = $ghostscript->guess($this->testFile);
        $this->assertEquals($version, self::NEW_VERSION);

        $ghostscript->convert($this->testFile, self::OLD_VERSION);
        $version = $ghostscript->guess($this->testFile);
        $this->assertEquals($version, self::OLD_VERSION);

        $ghostscript->convert($this->fakeFile, self::NEW_VERSION);
        $error = $ghostscript->getError();
        $this->assertNotEquals($error, '');

        $ghostscript->setOptions([
            '-dPDFSETTINGS' => '/screen',
            '-dNOPAUSE'
        ]);
        $ghostscript->convert($this->testFile, self::NEW_VERSION);
        $error = $ghostscript->getError();
        $this->assertNotEquals($error, '');

        $ghostscript->setBinPath($this->binPath);
        $ghostscript->setOptions([
            '-dCompatibilityLevel=test'
        ]);
        $ghostscript->convert($this->testFile, self::NEW_VERSION);
        $error = $ghostscript->getError();
        $this->assertNotEquals($error, '');

        $this->expectException('Exception');
        $ghostscript->setBinPath('');
        $ghostscript->convert($this->testFile, self::NEW_VERSION);
    }

    /**
     * Test delete temporary PDF
     * 
     * @return void
     */
    public function testDeleteTmpFile(): void
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $ghostscript->deleteTmpFile(true);
        $tmpFileCount = $ghostscript->getTmpFileCount();
        $this->assertEquals($tmpFileCount, 0);
    }
}
