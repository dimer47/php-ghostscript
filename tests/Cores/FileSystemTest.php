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
        rmdir($path);
    }

    /**
     * @return void
     */
    public function testPathShouldNotValid(): void
    {
        $fileSystem = new FileSystem();
        $this->assertEquals(false, $fileSystem->isValid('/var/mock/ghostscript'));
    }

    /**
     * @return void
     */
    public function testDirShouldExist(): void
    {
        $fileSystem = new FileSystem();
        $path = '/var/mock/ghostscript';
        mkdir($path, 0755, true);
        $this->assertEquals(true, $fileSystem->isDir($path));
        rmdir($path);
    }

    /**
     * @return void
     */
    public function testDirShouldNotExist(): void
    {
        $fileSystem = new FileSystem();
        $this->assertEquals(false, $fileSystem->isDir('/var/mock/ghostscript'));
    }

    /**
     * @return void
     */
    public function testFileShouldExist(): void
    {
        $fileSystem = new FileSystem();
        $path = '/var/mock/ghostscript';
        mkdir($path, 0755, true);
        $file = '/var/mock/ghostscript/test.txt';
        file_put_contents($file, 'test');
        $this->assertEquals(true, $fileSystem->isFile($file));
        unlink($file);
        rmdir($path);
    }

    /**
     * @return void
     */
    public function testFileShouldNotExist(): void
    {
        $fileSystem = new FileSystem();
        $this->assertEquals(false, $fileSystem->isFile('/var/mock/ghostscript/test.txt'));
    }
}
