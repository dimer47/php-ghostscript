<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Constants\GhostscriptConstant;
use Ordinary9843\Constants\MessageConstant;
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

    /** @var array */
    const PART_FILES = [
        __DIR__ . '/../files/part_1.pdf',
        __DIR__ . '/../files/part_2.pdf',
        __DIR__ . '/../files/part_3.pdf',
        __DIR__ . '/../files/part_4.pdf'
    ];

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
    public function testShouldContainsWhenGuess(): void
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $version = $ghostscript->guess(self::TEST_FILE);
        $this->assertContains($version, [
            GhostscriptConstant::STABLE_VERSION,
            self::TEST_VERSION
        ]);

        $version = $ghostscript->guess(self::FAKE_FILE);
        $this->assertEquals($version, 0);
        $this->assertNotEmpty($ghostscript->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     */
    public function testShouldEqualsWhenGuess(): void
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $version = $ghostscript->guess(self::FAKE_FILE);
        $this->assertEquals(0, $version);
        $this->assertNotEmpty($ghostscript->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     */
    public function testShouldFileExistsEqualsWhenConvert(): void
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $ghostscript->convert(self::TEST_FILE, self::TEST_VERSION);
        $version = $ghostscript->guess(self::TEST_FILE);
        $this->assertEquals(self::TEST_VERSION, $version);
        $this->assertFileExists(self::TEST_FILE);
        $this->assertEmpty($ghostscript->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     */
    public function testShouldFileNotExistsWhenConvert(): void
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $ghostscript->convert(self::FAKE_FILE, self::TEST_VERSION);
        $version = $ghostscript->guess(self::FAKE_FILE);
        $this->assertEquals(0, $version);
        $this->assertFileNotExists(self::FAKE_FILE);
        $this->assertNotEmpty($ghostscript->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     */
    public function testShouldFileNotPdfWhenConvert(): void
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $ghostscript->convert(self::OTHER_FILE, self::TEST_VERSION);
        $version = $ghostscript->guess(self::OTHER_FILE);
        $this->assertEquals(0, $version);
        $this->assertFileExists(self::OTHER_FILE);
        $this->assertNotEmpty($ghostscript->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     */
    public function testShouldConvertFailedWhenConvert(): void
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $ghostscript->setOptions([
            '-dCompatibilityLevel=test'
        ]);
        $originalVersion = $ghostscript->guess(self::TEST_FILE);
        $ghostscript->convert(self::TEST_FILE, self::TEST_VERSION);
        $this->assertEquals($originalVersion, $ghostscript->guess(self::TEST_FILE));
        $this->assertFileExists(self::TEST_FILE);
        $this->assertNotEmpty($ghostscript->getMessages()[MessageConstant::MESSAGE_TYPE_ERROR]);
    }

    /**
     * @return void
     */
    public function testShouldFileExistsWhenMerge(): void
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $files = array_merge([self::TEST_FILE], self::PART_FILES);
        $file = $ghostscript->merge(__DIR__ . '/../files/merge.pdf', $files);
        $this->assertFileExists($file);
        (is_file($file)) && unlink($file);
    }

    /**
     * @return void
     */
    public function testShouldMergeFailedWhenMerge(): void
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $ghostscript->setOptions([
            '-dCompatibilityLevel=test'
        ]);
        $files = array_merge([self::OTHER_FILE], self::PART_FILES);
        $file = $ghostscript->merge(__DIR__ . '/../files/merge.pdf', $files);
        $this->assertFileExists($file);
        (is_file($file)) && unlink($file);
    }
}
