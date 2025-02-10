<?php

namespace App\Service;

use App\DTO\ChannelDTO;
use App\Factory\YouTubeDTOFactory;
use App\Util\YouTubeHelper;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class YouTubeApiService
{
    private HttpClientInterface $httpClient;
    private string $apiUrl;
    private string $apiKey;
    private string $urlAvatar;

    public function __construct(
        HttpClientInterface $httpClient,
        string $apiUrl,
        string $apiKey,
        string $urlAvatar,
    ) {
        $this->httpClient = $httpClient;
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->apiKey = $apiKey;
        $this->urlAvatar = $urlAvatar;
    }

    public function fetchChannelDetails(string $handle): ?ChannelDTO
    {
        $identifier = $this->resolveChannelIdentifier($handle);

        $response = $this->httpClient->request('GET', "{$this->apiUrl}/channels", [
            'query' => [
                'part' => 'snippet',
                $identifier => $handle,
                'key' => $this->apiKey
            ]
        ]);

        $data = $response->toArray();

        return empty($data['items']) ? null : YouTubeDTOFactory::createChannelDTO($data['items'][0], $this->urlAvatar);
    }

    public function fetchVideoDetails(array $videoIds): array
    {
        if (empty($videoIds)) {
            return [];
        }

        $response = $this->httpClient->request('GET', "{$this->apiUrl}/videos", [
            'query' => [
                'id' => implode(',', $videoIds),
                'part' => 'snippet,contentDetails',
                'key' => $this->apiKey
            ]
        ]);

        $data = $response->toArray();

        $videos = [];
        foreach ($data['items'] ?? [] as $item) {
            $id = $item['id'];
            $videos[$id] = YouTubeDTOFactory::createVideoDTO($item);
        }

        return $videos;
    }

    private function resolveChannelIdentifier(string $identifier): string
    {
        if (is_null(YouTubeHelper::extractYouTubeHandle($identifier))) {
            return 'id';
        } else {
            return 'forHandle';
        }
    }
}
