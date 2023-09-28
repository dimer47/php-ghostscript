<?php

namespace Tests\Configs;

use PHPUnit\Framework\TestCase;
use ordinary9843\Cores\FileSystem;

class FileSystemTest extends TestCase
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
    public function testPathShouldValid(): void
    {
        $fileSystem = new FileSystem();
        $path = '/var/mock/ghostscript';
        mkdir($path, 0000, true);
        $this->assertEquals(true, $fileSystem->isValid($path));
        rmdir($path);
    }

    /**
     * @return void
     */
    public function testPathShouldInvalid(): void
    {
        $fileSystem = new FileSystem();
        $path = '/var/mock/ghostscript';
        $this->assertEquals(false, $fileSystem->isValid($path));
    }

    /**
     * @return void
     */
    public function testDirShouldValid(): void
    {
        $fileSystem = new FileSystem();
        $path = '/var/mock/ghostscript';
        mkdir($path, 0000, true);
        $this->assertEquals(true, $fileSystem->isDir($path));
        rmdir($path);
    }

    /**
     * @return void
     */
    public function testDirShouldInvalid(): void
    {
        $fileSystem = new FileSystem();
        $path = '/var/mock/ghostscript';
        $this->assertEquals(false, $fileSystem->isDir($path));
    }

    /**
     * @return void
     */
    public function testFileShouldValid(): void
    {
        $fileSystem = new FileSystem();
        $path = '/var/mock/ghostscript/test.txt';
        mkdir($path, 0000, true);
        $this->assertEquals(true, $fileSystem->isDir($path));
        rmdir($path);
    }

    /**
     * @return void
     */
    public function testFileShouldInvalid(): void
    {
        $fileSystem = new FileSystem();
        $path = '/var/mock/ghostscript/test.txt';
        $this->assertEquals(false, $fileSystem->isDir($path));
    }
}
