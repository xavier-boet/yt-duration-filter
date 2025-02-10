<?php

namespace App\Tests\Service;

use App\Entity\Video;
use App\Repository\VideoRepository;
use App\Service\VideoService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class VideoServiceTest extends TestCase
{
    private VideoService $videoService;
    private VideoRepository $videoRepository;

    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $this->videoRepository = $this->createMock(VideoRepository::class);

        $this->videoService = new VideoService($entityManager, $this->videoRepository);
    }

    public function testFilterNewRssVideos_AllVideosAreNew(): void
    {
        $rssVideos = [
            ['youtube_id' => 'ABC123', 'title' => 'Video 1'],
            ['youtube_id' => 'DEF456', 'title' => 'Video 2'],
        ];

        $this->videoRepository
            ->method('getVideosFromArrayYouTubeId')
            ->willReturn([]);

        $result = $this->videoService->filterNewRssVideos($rssVideos);

        $this->assertCount(2, $result);
        $this->assertEquals($rssVideos, array_values($result));
    }

    public function testFilterNewRssVideos_AllVideosAreKnown(): void
    {
        $rssVideos = [
            ['youtube_id' => 'ABC123', 'title' => 'Video 1'],
            ['youtube_id' => 'DEF456', 'title' => 'Video 2'],
        ];

        $knownVideos = [
            (new Video())->setYoutubeId('ABC123'),
            (new Video())->setYoutubeId('DEF456'),
        ];

        $this->videoRepository
            ->method('getVideosFromArrayYouTubeId')
            ->willReturn($knownVideos);

        $result = $this->videoService->filterNewRssVideos($rssVideos);

        $this->assertCount(0, $result);
    }

    public function testFilterNewRssVideos_SomeVideosAreKnown(): void
    {
        $rssVideos = [
            ['youtube_id' => 'ABC123', 'title' => 'Video 1'],
            ['youtube_id' => 'DEF456', 'title' => 'Video 2'],
            ['youtube_id' => 'GHI789', 'title' => 'Video 3'],
        ];

        $knownVideos = [
            (new Video())->setYoutubeId('ABC123'),
            (new Video())->setYoutubeId('DEF456'),
        ];

        $this->videoRepository
            ->method('getVideosFromArrayYouTubeId')
            ->willReturn($knownVideos);

        $result = $this->videoService->filterNewRssVideos($rssVideos);

        $this->assertCount(1, $result);
        $this->assertEquals([['youtube_id' => 'GHI789', 'title' => 'Video 3']], array_values($result));
    }
}
