<?php

namespace Tests\Configs;

use Exception;
use Ordinary9843\Configs\Config;
use ordinary9843\Cores\FileSystem;
use Tests\BaseTest;

class ConfigTest extends BaseTest
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
        $binPath = $this->getEnv('GS_BIN_PATH');
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
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws Exception
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
     * @throws \PHPUnit\Framework\MockObject\Exception
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
