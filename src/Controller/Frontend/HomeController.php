<?php

namespace App\Controller\Frontend;

use App\Service\VideoPaginationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends BaseVideoController
{
    public function __construct(private readonly VideoPaginationService $videoPaginationService) {}

    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $form = $this->getForm($request);
        $pagination = $this->videoPaginationService->getPaginatedVideos(
            $request,
            $request->query->get('duration')
        );

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }
}
