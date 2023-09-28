<?php

namespace Tests\Helpers;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Helpers\PathHelper;

class PathHelperTest extends TestCase
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
    public function testPathShouldEqualOriginPathAfterConversion(): void
    {
        $this->assertEquals(implode(DIRECTORY_SEPARATOR, ['usr', 'bin', 'gs']), PathHelper::convertPathSeparator('usr/bin/gs'));
    }
}