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
        $this->assertEquals("'usr/bin/sub path with spaces/gs'", PathHelper::convertPathSeparator('usr/bin/sub path with spaces/gs'));
        $this->assertEquals('usr/bin/sub-path-with-separator/gs', PathHelper::convertPathSeparator('usr/bin/sub-path-with-separator/gs'));
        $this->assertEquals('usr/bin/sub_path_with_underscore/gs', PathHelper::convertPathSeparator('usr/bin/sub_path_with_underscore/gs'));
    }
}
