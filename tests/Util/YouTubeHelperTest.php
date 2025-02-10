<?php

namespace App\Tests\Util;

use PHPUnit\Framework\TestCase;
use App\Util\YouTubeHelper;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class YouTubeHelperTest extends TestCase
{
    /**
     * @dataProvider validHandleProvider
     */
    public function testExtractYouTubeHandleWithValidUrls(string $url, string $expectedHandle): void
    {
        $this->assertSame($expectedHandle, YouTubeHelper::extractYouTubeHandle($url));
    }

    /**
     * @dataProvider invalidHandleProvider
     */
    public function testExtractYouTubeHandleWithInvalidUrls(string $url): void
    {
        $this->assertNull(YouTubeHelper::extractYouTubeHandle($url));
    }

    /**
     * @dataProvider validVideoIdProvider
     */
    public function testExtractYouTubeIdWithValidUrls(string $url, string $expectedId): void
    {
        $this->assertSame($expectedId, YouTubeHelper::extractYouTubeId($url));
    }

    /**
     * @dataProvider invalidVideoIdProvider
     */
    public function testExtractYouTubeIdWithInvalidUrls(string $url): void
    {
        $this->expectException(InvalidArgumentException::class);
        YouTubeHelper::extractYouTubeId($url);
    }

    // ---- Data Providers ----

    public static function validHandleProvider(): array
    {
        return [
            ["https://www.youtube.com/@FakeChannel123", "@FakeChannel123"],
            ["https://www.youtube.com/@ExampleHandleYT", "@ExampleHandleYT"],
            ["https://www.youtube.com/@Channel_Test_42", "@Channel_Test_42"],
            ["https://www.youtube.com/@random.handle.with.dots", "@random.handle.with.dots"],
        ];
    }

    public static function invalidHandleProvider(): array
    {
        return [
            ["https://www.youtube.com/c/SomeChannel"],
            ["https://www.youtube.com/user/SomeUser"],
            ["https://www.youtube.com/watch?v=abcd1234"],
            ["https://www.google.com"],
            ["random text"],
        ];
    }

    public static function validVideoIdProvider(): array
    {
        return [
            ["https://www.youtube.com/watch?v=abcdefghijk", "abcdefghijk"],
            ["https://youtu.be/abcdefghijk", "abcdefghijk"],
            ["https://www.youtube.com/embed/abcdefghijk", "abcdefghijk"],
            ["https://www.youtube.com/watch?v=12345678901&feature=youtu.be", "12345678901"],
        ];
    }

    public static function invalidVideoIdProvider(): array
    {
        return [
            ["https://www.youtube.com/"],
            ["https://www.youtube.com/channel/UC123456"],
            ["https://www.youtube.com/@SomeHandle"],
            ["https://www.example.com"],
            ["random string"],
        ];
    }
}
