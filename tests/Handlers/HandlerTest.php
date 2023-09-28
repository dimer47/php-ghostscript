<?php

namespace Tests\Handlers;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Configs\Config;
use Ordinary9843\Cores\FileSystem;
use Ordinary9843\Handlers\Handler;

class HandlerTest extends TestCase
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
    public function testSetConfigShouldEqualGetConfig(): void
    {
        $config = new Config([
            'binPath' => '/usr/bin/gs',
            'tmpPath' => sys_get_temp_dir()
        ]);
        $handler = new Handler();
        $handler->setConfig($config);
        $this->assertEquals($config, $handler->getConfig());
    }

    /**
     * @return void
     */
    public function testSetFileSystemShouldEqualGetFileSystem(): void
    {
        $fileSystem = new FileSystem();
        $handler = new Handler();
        $handler->setFileSystem($fileSystem);
        $this->assertEquals($fileSystem, $handler->getFileSystem());
    }

    /**
     * @return void
     */
    public function testSetOptionsShouldEqualGetOptions(): void
    {
        $options = [
            '-dSAFER'
        ];
        $handler = new Handler();
        $handler->setOptions($options);
        $this->assertEquals($options, $handler->getOptions());
    }

    /**
     * @return void
     */
    public function testTmpFilePrefixAndSuffixShouldMatched(): void
    {
        $handler = new Handler();
        $this->assertStringStartsWith('/tmp/ghostscript_tmp_file_', $handler->getTmpFile());
        $this->assertStringEndsWith('.pdf', $handler->getTmpFile());
    }

    /**
     * @return void
     */
    public function testTmpFileCountAfterClear(): void
    {
        $handler = new Handler();
        $handler->clearTmpFiles(true);
        $this->assertEquals(0, $handler->getTmpFileCount());
    }

    /**
     * @return void
     */
    public function testCommandIncludesAdditionalOptionsAfterConversion(): void
    {
        $handler = new Handler();
        $command = 'gs -sDEVICE=pdfwrite -dNOPAUSE';
        $this->assertEquals($command, $handler->optionsToCommand($command));

        $handler->setOptions([
            '-dSAFER'
        ]);
        $this->assertEquals($command . ' -dSAFER', $handler->optionsToCommand($command));
    }
}
