<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    public function testEMakesHtmlSafe(): void
    {
        $this->assertSame('&lt;b&gt;Tom &amp; Anna&lt;/b&gt;', e('<b>Tom & Anna</b>'));
    }

    public function testConfigReadsASettingWithDots(): void
    {
        $this->assertIsString(config('db.dsn'));
    }

    public function testConfigReturnsTheDefaultForAnUnknownKey(): void
    {
        $this->assertSame('fallback', config('does.not.exist', 'fallback'));
    }
}
