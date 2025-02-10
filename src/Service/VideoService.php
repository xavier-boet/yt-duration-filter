<?php

namespace App\Service;

use App\Entity\Video;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;

class VideoService
{
    public function __construct(
        private EntityManagerInterface $em,
        private VideoRepository $videoRepository
    ) {}

    /**
     * Filters out known videos from the RSS feed.
     *
     * @param array $rssVideos List of videos from RSS feed.
     * @return array List of new videos not yet in the database.
     */
    public function filterNewRssVideos(array $rssVideos): array
    {
        $knownVideos = $this->videoRepository->getVideosFromArrayYouTubeId($rssVideos);
        
        $knownIds = array_flip(array_map(fn(Video $video) => $video->getYoutubeId(), $knownVideos));

        return array_filter($rssVideos, fn($video) => !isset($knownIds[$video['youtube_id']]));
    }

    public function updateViewedStatus(Video $video, bool $viewed): void
    {
        $video->setIsViewed($viewed);
        $this->em->flush();
    }    
}
