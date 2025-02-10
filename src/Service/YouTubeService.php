<?php

namespace App\Service;

use App\Entity\Channel;
use App\Repository\ChannelRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Util\YouTubeHelper;

class YouTubeService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ChannelRepository $channelRepository,
        private RssService $rssService,
        private YouTubeApiService $youTubeApiService
    ) {}

    /**
     * Retrieves videos from a YouTube channel via RSS.
     *
     * @param string $channelYouTubeId The YouTube channel ID.
     * @return array List of videos with their metadata.
     */
    public function getVideosFromChannel(string $channelYouTubeId): array
    {
        $entries = $this->rssService->getEntries("https://www.youtube.com/feeds/videos.xml?channel_id={$channelYouTubeId}");

        $rssVideos = [];
        foreach ($entries as $entry) {
            $youtubeId = YouTubeHelper::extractYouTubeId($entry['link']);

            $rssVideos[$youtubeId] = [
                'youtube_id' => $youtubeId,
                'title' => $entry['title'],
                'publishedAt' => $entry['publishedAt']
            ];
        }

        return $rssVideos;
    }

    /**
     * Fetches video details from YouTube API and updates the database.
     *
     * @param Video[] $videos List of Video entities.
     */
    public function setVideosDetails(array $videos): void
    {
        $videoIds = array_map(fn($video) => $video->getYouTubeId(), $videos);
        $videosDetails = $this->youTubeApiService->fetchVideoDetails($videoIds);

        $newChannels = [];
        foreach ($videos as $video) {
            if (!isset($videosDetails[$video->getYouTubeId()])) {
                continue;
            }

            $videoDetails = $videosDetails[$video->getYouTubeId()];

            if (empty($video->getChannel())) {
                $channelYoutubeId = $videoDetails->channelId;
                if (!isset($newChannels[$channelYoutubeId])) {
                    $newChannels[$channelYoutubeId] = $this->getChannelDetailsAndSet($channelYoutubeId);
                }
                if (isset($newChannels[$channelYoutubeId])) {
                    $video->setChannel($newChannels[$channelYoutubeId]);
                }
            }
            if (empty($video->getTitle())) {
                $video->setTitle($videoDetails->title);
            }

            $video->setDuration($videoDetails->duration);
            $video->setPublishedAt($videoDetails->publishedAt);
        }

        $this->em->flush();
    }

    private function getChannelDetailsAndSet(string $channelYouTubeId)
    {
        $channel = $this->channelRepository->findOneBy(['youtubeId' => $channelYouTubeId]);

        if (!$channel) {
            /** @var ChannelDTO */
            $channelDetails = $this->youTubeApiService->fetchChannelDetails($channelYouTubeId);
            if ($channelDetails) {
                $channel = new Channel();
                $channel->setTitle($channelDetails->title);
                $channel->setYoutubeId($channelDetails->id);
                $channel->setHandle($channelDetails->handle);
                $channel->setThumbnail($channelDetails->thumbnail);
                $this->em->persist($channel);
            }
        }

        return $channel;
    }
}
