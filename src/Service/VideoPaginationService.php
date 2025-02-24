<?php

namespace App\Service;

use App\Entity\Channel;
use App\Exception\InvalidPageException;
use App\Repository\VideoRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Request;

class VideoPaginationService
{
    public function __construct(
        private PaginatorInterface $paginator,
        private VideoRepository $videoRepository
    ) {}

    public function getPaginatedVideos(Request $request, ?string $durationFilter = null, ?Channel $channel = null): PaginationInterface
    {
        $page = $request->query->get('page', '1');

        if (!filter_var($page, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            throw new InvalidPageException('Invalid page parameter');
        }

        $queryBuilder = $this->videoRepository->findVideos($durationFilter, $channel);

        return $this->paginator->paginate($queryBuilder, (int) $page);
    }
}
