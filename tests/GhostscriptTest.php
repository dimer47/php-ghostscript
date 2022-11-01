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
    const TEST_FILE = __DIR__ . '/../files/test.pdf';

    /** @var string */
    const FAKE_FILE = __DIR__ . '/../files/fake.pdf';

    /** @var string */
    protected $binPath = '/usr/bin/gs';

    /** @var string */
    protected $tmpPath = '';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tmpPath = sys_get_temp_dir();
    }

    /**
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
     * @return void
     */
    public function testGuess(): void
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $version = $ghostscript->guess(self::TEST_FILE);
        $this->assertContains($version, [
            self::OLD_VERSION,
            self::NEW_VERSION
        ]);

        $version = $ghostscript->guess(self::FAKE_FILE);
        $error = $ghostscript->getError();
        $this->assertEquals($version, 0);
        $this->assertNotEquals($error, '');
    }

    /**
     * @return void
     */
    public function testConvert(): void
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $ghostscript->convert(self::TEST_FILE, self::NEW_VERSION);
        $version = $ghostscript->guess(self::TEST_FILE);
        $this->assertEquals($version, self::NEW_VERSION);

        $ghostscript->convert(self::TEST_FILE, self::OLD_VERSION);
        $version = $ghostscript->guess(self::TEST_FILE);
        $this->assertEquals($version, self::OLD_VERSION);

        $ghostscript->convert(self::FAKE_FILE, self::NEW_VERSION);
        $error = $ghostscript->getError();
        $this->assertNotEquals($error, '');

        $ghostscript->setOptions([
            '-dPDFSETTINGS' => '/screen',
            '-dNOPAUSE'
        ]);
        $ghostscript->convert(self::TEST_FILE, self::NEW_VERSION);
        $error = $ghostscript->getError();
        $this->assertNotEquals($error, '');

        $ghostscript->setBinPath($this->binPath);
        $ghostscript->setOptions([
            '-dCompatibilityLevel=test'
        ]);
        $ghostscript->convert(self::TEST_FILE, self::NEW_VERSION);
        $error = $ghostscript->getError();
        $this->assertNotEquals($error, '');

        $this->expectException('Exception');
        $ghostscript->setBinPath('');
        $ghostscript->convert(self::TEST_FILE, self::NEW_VERSION);
    }

    /**
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
