<?php

namespace App\Controller\Api;

use App\Entity\Video;
use App\Util\YouTubeHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/videos', name: 'app_api_videos_')]
final class VideoController extends AbstractController
{
    // #[Route('/api/videos', name: 'app_api_videos_get', methods: ['GET'])]
    // #[Route('/api/videos/{id}', name: 'app_api_videos_get_one', methods: ['GET'])]
    // #[Route('/api/videos/{id}', name: 'app_api_videos_delete', methods: ['DELETE'])]
    // #[Route('', name: 'get', methods: ['GET'])]
    // public function getVideo(): JsonResponse
    // {
    //     return $this->json(['video' => []]);
    // }

    #[Route('', name: 'post', methods: ['POST'])]
    public function postVideo(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $url = $data['url'] ?? null;

        if (!$url) {
            return new JsonResponse(['error' => 'Missing URL'], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $videoId = YouTubeHelper::extractYouTubeId($url);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => 'Invalid URL'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $existingVideo = $em->getRepository(Video::class)->findOneBy(['youtubeId' => $videoId]);
        if ($existingVideo) {
            return new JsonResponse(['error' => 'Video already added'], JsonResponse::HTTP_CONFLICT);
        }

        $video = new Video();
        $video->setYoutubeId($videoId);
        $em->persist($video);
        $em->flush();

        return new JsonResponse(
            ['message' => 'YouTube video successfully added', 'videoId' => $videoId],
            JsonResponse::HTTP_CREATED
        );
    }
}
