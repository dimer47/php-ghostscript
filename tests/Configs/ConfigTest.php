<?php

namespace Tests\Configs;

use Exception;
use PHPUnit\Framework\TestCase;
use Ordinary9843\Configs\Config;
use ordinary9843\Cores\FileSystem;

class ConfigTest extends TestCase
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
    public function testSetBinPathShouldEqualGetBinPath(): void
    {
        $binPath = '/usr/bin/gs';
        $config = new Config();
        $config->setBinPath($binPath);
        $this->assertEquals($binPath, $config->getBinPath());
    }

    /**
     * @return void
     */
    public function testSetTmpPathShouldEqualGetTmpPath(): void
    {
        $tmpPath = sys_get_temp_dir();
        $config = new Config();
        $config->setTmpPath($tmpPath);
        $this->assertEquals($tmpPath, $config->getTmpPath());
    }

    /**
     * @return void
     */
    public function testBinPathShouldExist(): void
    {
        $fileSystem = $this->createMock(FileSystem::class);
        $fileSystem->method('isValid')->willReturn(true);
        $config = new Config([
            'fileSystem' => $fileSystem
        ]);
        $this->assertNull($config->validateBinPath());
    }

    /**
     * @return void
     */
    public function testBinPathShouldNotExist(): void
    {
        $fileSystem = $this->createMock(FileSystem::class);
        $fileSystem->method('isValid')->willReturn(false);
        $config = new Config([
            'fileSystem' => $fileSystem
        ]);
        $this->expectException(Exception::class);
        $this->assertNull($config->validateBinPath());
    }
}
