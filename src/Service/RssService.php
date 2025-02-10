<?php

namespace App\Service;

use RuntimeException;

class RssService
{
    /**
     * Fetches RSS feed and returns parsed entries.
     *
     * @param string $url The RSS feed URL.
     * @return array List of parsed entries.
     * @throws RuntimeException If the feed cannot be retrieved or parsed.
     */
    public function getEntries(string $url): array
    {
        $xmlContent = $this->fetchFeed($url);
        return $this->parseXml($xmlContent);
    }

    /**
     * Fetches the XML content from a given URL.
     *
     * @param string $url
     * @return string
     * @throws RuntimeException If the RSS feed cannot be loaded.
     */
    protected function fetchFeed(string $url): string
    {
        $content = @file_get_contents($url);

        if ($content === false) {
            throw new RuntimeException("Failed to fetch RSS feed from: $url");
        }

        return $content;
    }

    /**
     * Parses an XML string and extracts entries.
     *
     * @param string $xmlContent
     * @return array
     * @throws RuntimeException If the XML is invalid.
     */
    protected function parseXml(string $xmlContent): array
    {
        $feed = simplexml_load_string($xmlContent);

        if ($feed === false) {
            throw new RuntimeException("Invalid XML format.");
        }

        $entries = [];
        foreach ($feed->entry as $entry) {
            $entries[] = [
                'title'       => (string) $entry->title,
                'link'        => (string) $entry->link['href'],
                'publishedAt' => (string) $entry->published,
            ];
        }

        return $entries;
    }
}
