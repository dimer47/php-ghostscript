<?php

namespace Tests\Helpers;

use Ordinary9843\Helpers\PathHelper;
use Tests\BaseTest;

class PathHelperTest extends BaseTest
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
