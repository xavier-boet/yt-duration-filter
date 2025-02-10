<?php

namespace App\Util;

use Symfony\Component\Validator\Exception\InvalidArgumentException;

class YouTubeHelper
{
    public static function convertYouTubeDuration(string $duration): int
    {
        $interval = new \DateInterval($duration);
        return ($interval->h * 60) + $interval->i;
    }

    /**
     * Extracts the YouTube handle from a given channel URL.
     *
     * @param string $url The YouTube channel URL.
     * @return string|null The extracted handle or null if not found.
     */
    public static function extractYouTubeHandle(string $url): ?string
    {
        //$pattern = '/https?:\/\/(?:www\.)?youtube\.com\/(@[a-zA-Z0-9_\-.\·]+)/';
        $pattern = '/@[\w.\-·]+/';
        preg_match($pattern, $url, $matches);
        return $matches[0] ?? null;
    }

    /**
     * Extracts the YouTube video ID from a given URL.
     *
     * @param string $url The YouTube video URL.
     * @return string The extracted video ID.
     * @throws InvalidArgumentException If the URL is invalid.
     */
    public static function extractYouTubeId(string $url): string
    {
        if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/(youtube\.com|youtu\.be)/', $url)) {
            throw new InvalidArgumentException("Invalid YouTube URL");
        }

        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.*|.*[?&]v=)|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/';
        preg_match($pattern, $url, $matches);

        if (!isset($matches[1])) {
            throw new InvalidArgumentException("Could not extract YouTube ID");
        }

        return $matches[1];
    }
}
