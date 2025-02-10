<?php

namespace App\Controller\Frontend;

use App\Form\VideoFilterType;
use App\Repository\VideoRepository;
use App\Service\YouTubeApiService;
use App\Service\YouTubeService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    // #[Route('/test', name: 'app_test')]
    // public function test(
    //     YouTubeApiService $youTubeApiService,
    //     VideoRepository $videoRepository,
    //     YouTubeService $youTubeService
    //     )
    // {
    //     // $entries = $videoRepository->getEntriesWithoutDuration();
    //     // $youTubeService->setVideoDetails($entries);
    //     //$youTubeApiService->fetchChannelDetails('https://www.youtube.com/@benoit_lecorre');
    // }

    #[Route('/', name: 'app_home')]
    public function index(Request $request, VideoRepository $videoRepository, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(VideoFilterType::class);
        $form->handleRequest($request);

        $durationFilter = $request->query->get('duration');
        $queryBuilder = $videoRepository->findByDuration($durationFilter);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1)
        );

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }

    //search
}
