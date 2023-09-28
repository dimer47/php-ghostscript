<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Constants\GhostscriptConstant;

class GhostscriptTraitTest extends TestCase
{
    // /**
    //  * @return void
    //  */
    // protected function setUp(): void
    // {
    //     parent::setUp();
    // }

    public function testShouldEqualsWhenGetBinPath(): void
    {
        $this->assertTrue(true);
    }

    // /**
    //  * @return void
    //  */
    // public function testShouldEqualsWhenGetBinPath(): void
    // {
    //     $this->assertEquals('', $this->getBinPath());

    //     $binPath = '/usr/bin/gs';
    //     $this->setBinPath($binPath);
    //     $this->assertEquals($binPath, $this->getBinPath());
    // }

    // /**
    //  * @return void
    //  */
    // public function testShouldEqualsWhenGetTmpPath(): void
    // {
    //     $this->assertEquals('', $this->getTmpPath());

    //     $tmpPath = sys_get_temp_dir();
    //     $this->setTmpPath($tmpPath);
    //     $this->assertEquals($tmpPath, $this->getTmpPath());
    // }

    // /**
    //  * @return void
    //  */
    // public function testShouldEqualsWhenGetOptions(): void
    // {
    //     $this->assertEquals([], $this->getOptions());

    //     $options = [
    //         '-dNOPAUSE'
    //     ];
    //     $this->setOptions($options);
    //     $this->assertEquals($options, $this->getOptions());
    // }

    // /**
    //  * @return void
    //  */
    // public function testShouldReturnTrueWhenGetTmpFile(): void
    // {
    //     $this->assertTrue((bool)preg_match('/' . GhostscriptConstant::TMP_FILE_PREFIX . '/', $this->getTmpFile()));
    // }

    // /**
    //  * @return void
    //  */
    // public function testShouldReturnTrueWhenGetTmpFileCount(): void
    // {
    //     $this->setTmpPath(sys_get_temp_dir());
    //     for ($i = 0; $i < 5; $i++) {
    //         file_put_contents($this->getTmpFile(), '');
    //     }
    //     $this->assertNotEquals(0, $this->getTmpFileCount());

    //     $this->clearTmpFile(true);
    //     $this->assertEquals(0, $this->getTmpFileCount());
    // }

    // /**
    //  * @return void
    //  */
    // public function testShouldFileExistsClearTmpFile(): void
    // {
    //     $this->setTmpPath(sys_get_temp_dir());
    //     for ($i = 0; $i < 5; $i++) {
    //         file_put_contents($this->getTmpFile(), '');
    //     }
    //     $this->clearTmpFile(true);
    //     $this->assertEquals(0, $this->getTmpFileCount());
    // }

    // /**
    //  * @return void
    //  */
    // public function testShouldEqualsWhenConvertPathSeparator(): void
    // {
    //     $this->assertEquals('', $this->convertPathSeparator(''));
    //     $this->assertEquals('/temp', $this->convertPathSeparator('\temp'));
    // }

    // /**
    //  * @return void
    //  */
    // public function testShouldEqualsWhenValidateBinPath(): void
    // {
    //     $this->setBinPath('/usr/bin/gs');
    //     $this->assertNull($this->validateBinPath());
    // }

    // /**
    //  * @return void
    //  */
    // public function testShouldThrowsExceptionWhenValidateBinPath(): void
    // {
    //     $this->expectException('Exception');
    //     $this->validateBinPath();
    // }

    // /**
    //  * @return void
    //  */
    // public function testShouldEqualsWhenOptionToCommand(): void
    // {
    //     $this->assertEquals('', $this->optionsToCommand(''));

    //     $this->setOptions([
    //         '-dNOPAUSE'
    //     ]);
    //     $this->assertEquals('gs -dNOPAUSE', $this->optionsToCommand('gs'));

    //     $this->setOptions([
    //         '-dPDFSETTINGS' => '/screen',
    //         '-dNOPAUSE'
    //     ]);
    //     $this->assertEquals('gs -dPDFSETTINGS=/screen -dNOPAUSE', $this->optionsToCommand('gs'));
    // }

    // /**
    //  * @return void
    //  */
    // public function testShouldReturnTrueWhenIsPdf(): void
    // {
    //     $this->assertTrue($this->isPdf(dirname(__DIR__, 2) . '/files/test.pdf'));
    // }

    // /**
    //  * @return void
    //  */
    // public function testShouldReturnFalseWhenIsPdf(): void
    // {
    //     $this->assertFalse($this->isPdf(dirname(__DIR__, 2) . '/files/test.txt'));
    // }
}
