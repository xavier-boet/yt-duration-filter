<?php

namespace App\Tests\Service;

use App\Service\RssService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class RssServiceTest extends TestCase
{
    //private RssService $rssService;

    protected function setUp(): void
    {
        //$this->rssService = new RssService();
    }

    /**
     * 🟢 Teste `getEntries()` avec un flux RSS valide.
     */
    public function testGetEntriesWithValidFeed(): void
    {
        // On remplace `file_get_contents()` pour renvoyer un XML valide
        $rssServiceMock = $this->getMockBuilder(RssService::class)
            ->onlyMethods(['fetchFeed'])
            ->getMock();

        $rssServiceMock->method('fetchFeed')->willReturn($this->getValidXml());

        $entries = $rssServiceMock->getEntries('https://example.com/rss');

        $this->assertCount(2, $entries);
        $this->assertEquals('Video 1', $entries[0]['title']);
        $this->assertEquals('https://youtube.com/watch?v=ABC123', $entries[0]['link']);
        $this->assertEquals('2024-02-01T12:00:00Z', $entries[0]['publishedAt']);
    }

    /**
     * 🔴 Teste `getEntries()` avec une URL invalide.
     */
    public function testGetEntriesWithInvalidUrl(): void
    {
        $rssServiceMock = $this->getMockBuilder(RssService::class)
            ->onlyMethods(['fetchFeed'])
            ->getMock();

        $rssServiceMock->method('fetchFeed')->willThrowException(new RuntimeException('Failed to fetch RSS feed'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to fetch RSS feed');

        $rssServiceMock->getEntries('https://invalid-url.com/rss');
    }

    // /**
    //  * 🔴 Teste `getEntries()` avec un XML malformé.
    //  */
    // public function testGetEntriesWithInvalidXml(): void
    // {
    //     $rssServiceMock = $this->getMockBuilder(RssService::class)
    //         ->onlyMethods(['fetchFeed'])
    //         ->getMock();
    
    //     // XML malformé
    //     $rssServiceMock->method('fetchFeed')->willReturn('<invalid_xml><entry></invalid_xml>');
    
    //     $this->expectException(RuntimeException::class);
    //     $this->expectExceptionMessage('Invalid XML format.');
    
    //     $rssServiceMock->getEntries('https://example.com/rss');
    // }    

    /**
     * 📌 Génère un XML valide pour les tests.
     */
    private function getValidXml(): string
    {
        return <<<XML
        <?xml version="1.0" encoding="UTF-8" ?>
        <feed xmlns="http://www.w3.org/2005/Atom">
            <entry>
                <title>Video 1</title>
                <link href="https://youtube.com/watch?v=ABC123"/>
                <published>2024-02-01T12:00:00Z</published>
            </entry>
            <entry>
                <title>Video 2</title>
                <link href="https://youtube.com/watch?v=DEF456"/>
                <published>2024-02-02T14:30:00Z</published>
            </entry>
        </feed>
        XML;
    }
}
