<?php

namespace App\Factory;

use App\DTO\VideoDTO;
use App\DTO\ChannelDTO;
use App\Util\YouTubeHelper;

class YouTubeDTOFactory
{
    public static function createVideoDTO(array $data): VideoDTO
    {
        return new VideoDTO(
            id: $data['id'],
            channelId: $data['snippet']['channelId'],
            title: $data['snippet']['title'],
            duration: YouTubeHelper::convertYouTubeDuration($data['contentDetails']['duration']),
            publishedAt: new \DateTimeImmutable($data['snippet']['publishedAt'])
        );
    }

    public static function createChannelDTO(array $data, ?string $baseAvatarUrl = 'https://yt3.ggpht.com/'): ChannelDTO
    {
        $channel = $data['snippet'];
        $thumbnailUrl = $channel['thumbnails']['default']['url'] ?? null;

        if ($thumbnailUrl && str_starts_with($thumbnailUrl, $baseAvatarUrl)) {
            $thumbnailUrl = substr($thumbnailUrl, strlen($baseAvatarUrl));
        }

        return new ChannelDTO(
            id: $data['id'],
            title: $channel['title'],
            handle: $channel['customUrl'] ?? null,
            thumbnail: $thumbnailUrl
        );
    }
}
