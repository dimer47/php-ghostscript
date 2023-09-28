<?php

namespace Tests\Cores;

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
        mkdir($path, 0755, true);
        $this->assertEquals(true, $fileSystem->isValid($path));
        @rmdir($path);
    }

    /**
     * @return void
     */
    public function testPathShouldInvalid(): void
    {
        $fileSystem = new FileSystem();
        $path = '/var/mock/ghostscript';
        @rmdir($path);
        $this->assertEquals(false, $fileSystem->isValid($path));
    }

    /**
     * @return void
     */
    public function testDirShouldValid(): void
    {
        $fileSystem = new FileSystem();
        $path = '/var/mock/ghostscript';
        mkdir($path, 0755, true);
        $this->assertEquals(true, $fileSystem->isDir($path));
        @rmdir($path);
    }

    /**
     * @return void
     */
    public function testDirShouldInvalid(): void
    {
        $fileSystem = new FileSystem();
        $path = '/var/mock/ghostscript';
        @rmdir($path);
        $this->assertEquals(false, $fileSystem->isDir($path));
    }

    /**
     * @return void
     */
    public function testFileShouldValid(): void
    {
        $fileSystem = new FileSystem();
        $path = '/var/mock/ghostscript';
        mkdir($path, 0755, true);
        $file = '/var/mock/ghostscript/test.txt';
        file_put_contents($file, 'test');
        $this->assertEquals(true, $fileSystem->isFile($file));
        @unlink($file);
        @rmdir($path);
    }

    /**
     * @return void
     */
    public function testFileShouldInvalid(): void
    {
        $fileSystem = new FileSystem();
        $file = '/var/mock/ghostscript/test.txt';
        @unlink($file);
        $this->assertEquals(false, $fileSystem->isFile($file));
    }
}
