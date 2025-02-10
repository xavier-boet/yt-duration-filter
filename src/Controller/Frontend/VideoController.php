<?php

namespace App\Controller\Frontend;

use App\Entity\Video;
use App\Service\VideoService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VideoController extends AbstractController
{
    public function __construct(
        private VideoService $videoService
    ) {}

    #[Route('/videos/{youtubeId}', name: 'app_video_view', requirements: ['youtubeId' => '[a-zA-Z0-9_-]{11}'])]
    public function view(
        #[MapEntity(mapping: ['youtubeId' => 'youtubeId'])]
        Video $video
    ): Response {
        $this->videoService->updateViewedStatus($video, true);

        return $this->redirect(
            $this->getParameter('youtube_url_watch')
                .
                $video->getYoutubeId()
        );
    }
}
