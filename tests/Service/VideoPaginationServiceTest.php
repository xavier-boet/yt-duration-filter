<?php

namespace App\Tests\Service;

use App\Entity\Channel;
use App\Exception\InvalidPageException;
use App\Repository\VideoRepository;
use App\Service\VideoPaginationService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class VideoPaginationServiceTest extends TestCase
{
    private $videoRepositoryMock;
    private $paginatorMock;
    private $service;

    protected function setUp(): void
    {
        $this->videoRepositoryMock = $this->createMock(VideoRepository::class);
        $this->paginatorMock = $this->createMock(PaginatorInterface::class);

        $this->service = new VideoPaginationService(
            $this->paginatorMock,
            $this->videoRepositoryMock
        );
    }

    public function testGetPaginatedVideosWithNoFilters()
    {
        $request = new Request(['page' => 1]);

        $queryBuilderMock = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $this->videoRepositoryMock
            ->expects($this->once())
            ->method('findVideos')
            ->with(null, null)
            ->willReturn($queryBuilderMock);

        $paginationMock = $this->createMock(PaginationInterface::class);
        $paginationMock
            ->method("getCurrentPageNumber")
            ->willReturn(1);
        $this->paginatorMock
            ->expects($this->once())
            ->method('paginate')
            ->with($queryBuilderMock, 1)
            ->willReturn($paginationMock);

        $result = $this->service->getPaginatedVideos($request);

        $this->assertInstanceOf(PaginationInterface::class, $result);
        $this->assertEquals(1, $result->getCurrentPageNumber());
    }

    public function testGetPaginatedVideosWithFilters()
    {
        $request = new Request(['page' => 2]);
        $durationFilter = 'short';
        $channel = new Channel();

        $queryBuilderMock = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $this->videoRepositoryMock
            ->expects($this->once())
            ->method('findVideos')
            ->with($durationFilter, $channel)
            ->willReturn($queryBuilderMock);

        $paginationMock = $this->createMock(PaginationInterface::class);
        $paginationMock
            ->method('getCurrentPageNumber')
            ->willReturn(2);
        $this->paginatorMock
            ->expects($this->once())
            ->method('paginate')
            ->with($queryBuilderMock, 2)
            ->willReturn($paginationMock);

        $result = $this->service->getPaginatedVideos($request, $durationFilter, $channel);

        $this->assertInstanceOf(PaginationInterface::class, $result);
        $this->assertEquals(2, $result->getCurrentPageNumber());
    }

    public function testGetPaginatedVideosWithInvalidPage(): void
    {
        $request = new Request(['page' => 'invalid']);

        $this->videoRepositoryMock
            ->expects($this->never())
            ->method('findVideos');
        $this->paginatorMock
            ->expects($this->never())
            ->method('paginate');

        $this->expectException(InvalidPageException::class);
        $this->expectExceptionMessage('Invalid page parameter');

        $this->service->getPaginatedVideos($request);
    }
}
