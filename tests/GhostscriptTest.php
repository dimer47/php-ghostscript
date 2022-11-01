<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Ghostscript;

class GhostscriptTest extends TestCase
{
    /** @var float */
    const TEST_VERSION = 1.5;

    /** @var string */
    const TEST_FILE = __DIR__ . '/../files/test.pdf';

    /** @var string */
    const FAKE_FILE = __DIR__ . '/../files/fake.pdf';

    /** @var string */
    const OTHER_FILE = __DIR__ . '/../files/test.txt';

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
            Ghostscript::STABLE_VERSION,
            self::TEST_VERSION
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
        $ghostscript->convert(self::TEST_FILE, self::TEST_VERSION);
        $version = $ghostscript->guess(self::TEST_FILE);
        $this->assertEquals($version, self::TEST_VERSION);

        $ghostscript->convert(self::TEST_FILE, Ghostscript::STABLE_VERSION);
        $version = $ghostscript->guess(self::TEST_FILE);
        $this->assertEquals($version, Ghostscript::STABLE_VERSION);

        $ghostscript->convert(self::FAKE_FILE, self::TEST_VERSION);
        $error = $ghostscript->getError();
        $this->assertNotEquals($error, '');

        $ghostscript->setOptions([
            '-dPDFSETTINGS' => '/screen',
            '-dNOPAUSE'
        ]);
        $ghostscript->convert(self::TEST_FILE, self::TEST_VERSION);
        $error = $ghostscript->getError();
        $this->assertNotEquals($error, '');

        $ghostscript->setBinPath($this->binPath);
        $ghostscript->setOptions([
            '-dCompatibilityLevel=test'
        ]);
        $ghostscript->convert(self::TEST_FILE, self::TEST_VERSION);
        $error = $ghostscript->getError();
        $this->assertNotEquals($error, '');

        $ghostscript->convert(self::OTHER_FILE, self::TEST_VERSION);
        $error = $ghostscript->getError();
        $this->assertNotEquals($error, '');

        $this->expectException('Exception');
        $ghostscript->setBinPath('');
        $ghostscript->convert(self::TEST_FILE, self::TEST_VERSION);
    }

    /**
     * @return void
     */
    public function testMerge(): void
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $files = [
            self::OTHER_FILE,
            $ghostscript->convert(__DIR__ . '/../files/part_1.pdf', self::TEST_VERSION),
            __DIR__ . '/../files/part_2.pdf',
            __DIR__ . '/../files/part_3.pdf',
            __DIR__ . '/../files/part_4.pdf'
        ];
        $file = $ghostscript->merge(__DIR__ . '/../files/merge.pdf', $files);
        $this->assertFileExists($file);

        array_pop($files);
        $file = $ghostscript->merge(__DIR__ . '/../files/merge.pdf', $files);
        $this->assertFileExists($file);

        $ghostscript->setOptions([
            '-dPDFSETTINGS' => '/screen'
        ]);
        $file = $ghostscript->merge(__DIR__ . '/../files/merge.pdf', $files);
        $error = $ghostscript->getError();
        $this->assertNotEquals($error, '');

        $ghostscript->setOptions([
            '-dCompatibilityLevel=test'
        ]);
        $file = $ghostscript->merge(__DIR__ . '/../files/merge.pdf', $files);
        $error = $ghostscript->getError();
        $this->assertNotEquals($error, '');

        if (is_file($file)) {
            unlink($file);
        }
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

    /**
     * @return void
     */
    public function testIsPdf(): void
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $this->assertTrue($ghostscript->isPdf(self::TEST_FILE));
        $this->assertFalse($ghostscript->isPdf(self::OTHER_FILE));
    }
}
