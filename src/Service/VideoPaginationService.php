<?php

namespace App\Service;

use App\Entity\Channel;
use App\Repository\VideoRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class VideoPaginationService
{
    public function __construct(
        private PaginatorInterface $paginator,
        private VideoRepository $videoRepository
    ) {}

    public function getPaginatedVideos(Request $request, ?string $durationFilter = null, ?Channel $channel = null)
    {
        $queryBuilder = $this->videoRepository->findVideos($durationFilter, $channel);
    
        return $this->paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1)
        );
    }    
}
