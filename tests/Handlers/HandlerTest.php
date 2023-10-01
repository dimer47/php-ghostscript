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
        $handler->getConfig()->setFileSystem($fileSystem);
        $this->assertEquals($fileSystem, $handler->getConfig()->getFileSystem());
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
    public function testTmpFileShouldHaveCorrectFormat(): void
    {
        $handler = new Handler();
        $this->assertStringStartsWith('/tmp/ghostscript_tmp_file_', $handler->getTmpFile());
        $this->assertStringEndsWith('.pdf', $handler->getTmpFile());
    }

    /**
     * @return void
     */
    public function testCommandShouldIncludeOptions(): void
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

    /**
     * @return void
     */
    public function testGetPdfTotalPageShouldReturnGreaterThanZero(): void
    {
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $config = new Config([
            'binPath' => '/usr/bin/gs'
        ]);
        $handler = new Handler($config);
        $this->assertGreaterThan(0, $handler->getPdfTotalPage($file));
    }

    /**
     * @return void
     */
    public function testGetPdfTotalPageShouldReturnLessThanOrEqualZero(): void
    {
        $fileSystem = $this->createMock(FileSystem::class);
        $fileSystem->method('isFile')->willReturn(false);
        $file = dirname(__DIR__, 2) . '/files/test.pdf';
        $handler = new Handler(new Config([
            'binPath' => '/usr/bin/gs',
            'fileSystem' => $fileSystem
        ]));
        $this->assertLessThanOrEqual(0, $handler->getPdfTotalPage($file));

        $handler = new Handler(new Config([
            'binPath' => '/usr/test/gs'
        ]));
        $this->assertLessThanOrEqual(0, $handler->getPdfTotalPage($file));

        $handler = $this->getMockBuilder(Handler::class)
            ->setConstructorArgs([new Config(['binPath' => '/usr/bin/gs'])])
            ->setMethods(['isPdf'])
            ->getMock();
        $handler->method('isPdf')->willReturn(false);
        $this->assertLessThanOrEqual(0, $handler->getPdfTotalPage($file));
    }

    /**
     * @return void
     */
    public function testIsPdfShouldReturnTrue(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($file, '%PDF-');
        rename($file, $file .= '.pdf');
        $handler = new Handler();
        $this->assertTrue($handler->isPdf($file));
        @unlink($file);
    }

    /**
     * @return void
     */
    public function testIsPdfShouldReturnFalse(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'txt');
        file_put_contents($file, 'txt');
        rename($file, $file .= '.txt');
        $handler = new Handler();
        $this->assertFalse($handler->isPdf($file));
        @unlink($file);
    }
}
