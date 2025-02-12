<?php

namespace App\Repository;

use App\Entity\Channel;
use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
//use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Repository for managing Video entity.
 * 
 * @extends ServiceEntityRepository<Video>
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Video::class);
    }

    /**
     * Excludes videos with null duration.
     */
    private function excludeNullDuration(QueryBuilder $qb): QueryBuilder
    {
        return $qb->andWhere('v.duration IS NOT NULL');
    }

    private function excludeViewed(QueryBuilder $qb): QueryBuilder
    {
        return $qb->andWhere('v.isViewed = false');
    }

    private function orderByPublishedAt(QueryBuilder $qb): QueryBuilder
    {
        return $qb->orderBy('v.publishedAt', 'DESC');
    }

    /**
     * Adds new videos to a given channel and persists them in the database.
     * 
     * @param Channel $channel The channel to associate videos with.
     * @param array $rssVideos An array of video data retrieved from RSS feed.
     */
    public function addVideos(Channel $channel, array $rssVideos): void
    {
        $em = $this->getEntityManager();

        foreach ($rssVideos as $youtubeId => $entry) {
            $video = new Video();
            $video->setChannel($channel)
                ->setYoutubeId($youtubeId)
                ->setTitle($entry['title'])
                ->setPublishedAt(new \DateTimeImmutable($entry['publishedAt']));

            $em->persist($video);
        }

        $em->flush();
    }

    // public function paginateVideos(int $page, int $limit): PaginationInterface
    // {
    //     return $this->paginator->paginate(
    //         $this->createQueryBuilder('v'),
    //         $page,
    //         $limit
    //     );
    // }

    /**
     * Retrieves all videos that have a duration set.
     * 
     * @return Video[]
     */
    public function findVideosWithDuration(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('v');
        $this->excludeNullDuration($qb);
        $this->excludeViewed($qb);
        $this->orderByPublishedAt($qb);

        return $qb;
    }

    /**
     * Finds videos by a specific duration range.
     * 
     * @param string|null $durationFilter The duration range filter (e.g., "10-30", "60+").
     * @return Video[]
     */
    public function findVideos(?string $durationFilter = null, ?Channel $channel = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('v');
        $this->excludeNullDuration($qb);
        $this->excludeViewed($qb);

        if ($durationFilter) {
            if ($durationFilter === '60+') {
                $qb->andWhere('v.duration > 60');
            } else {
                $parts = explode('-', $durationFilter);

                if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                    $min = (int) $parts[0];
                    $max = (int) $parts[1];

                    if ($min > $max) {
                        [$min, $max] = [$max, $min];
                    }

                    $qb->andWhere('v.duration BETWEEN :min AND :max')
                        ->setParameter('min', $min)
                        ->setParameter('max', $max);
                }
            }
        }

        $this->orderByPublishedAt($qb);

        if ($channel) {
            $qb->andWhere('v.channel = :channel')
                ->setParameter('channel', $channel);
        }

        return $qb;
    }


    /**
     * Retrieves videos based on an array of YouTube IDs.
     * 
     * @param array $items Array of YouTube video IDs as keys.
     * @return Video[]
     */
    public function getVideosFromArrayYouTubeId(array $items): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.youtubeId IN (:youtube_ids)')
            ->setParameter('youtube_ids', array_keys($items))
            ->getQuery()
            ->getResult();
    }

    /**
     * Retrieves videos that do not have a duration set.
     * 
     * @return Video[]
     */
    public function getEntriesWithoutDuration(): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.duration IS NULL')
            ->getQuery()
            ->getResult();
    }
}
