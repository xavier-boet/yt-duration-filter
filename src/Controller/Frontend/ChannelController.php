<?php

namespace App\Controller\Frontend;

use App\Entity\Channel;
use App\Exception\InvalidPageException;
use App\Service\VideoPaginationService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ChannelController extends BaseVideoController
{
    public function __construct(private readonly VideoPaginationService $videoPaginationService) {}

    #[Route('/{handle}', name: 'app_channel', requirements: ['handle' => '@[\w.\-·]+'])]
    public function index(
        Request $request,
        #[MapEntity(mapping: ['handle' => 'handle'])]
        Channel $channel
    ): Response {
        $form = $this->getForm($request);

        try {
            $pagination = $this->videoPaginationService->getPaginatedVideos(
                $request,
                $request->query->get('duration'),
                $channel
            );
        } catch (InvalidPageException) {
            return $this->redirectToRoute('app_channel', ['handle' => $channel->getHandle()]);
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }
}
