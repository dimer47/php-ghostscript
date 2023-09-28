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
        $path = '/use/bin/gs';
        $this->assertEquals($path, PathHelper::convertPathSeparator($path));
    }
}
