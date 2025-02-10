<?php

namespace App\Controller\Api;

use App\Entity\Channel;
use App\Service\YouTubeApiService;
use App\Util\YouTubeHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/channels', name: 'api_channels_')]
class ChannelController extends AbstractController
{
    // #[Route('/', name: 'get', methods: ['GET'])]
    // public function list(): JsonResponse
    // {
    //     return $this->json(['channels' => []]);
    // }

    #[Route('', name: 'post', methods: ['POST'])]
    public function postChannel(
        Request $request,
        EntityManagerInterface $em,
        YouTubeApiService $youTubeApiService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $url = $data['url'] ?? null;

        if (!$url) {
            return new JsonResponse(['error' => 'Missing URL'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $handle = YouTubeHelper::extractYouTubeHandle($url);

        if (!$handle) {
            return new JsonResponse(['error' => 'Invalid URL'], JsonResponse::HTTP_BAD_REQUEST);
        }

        /** @var Channel */
        $existingChannel = $em->getRepository(Channel::class)->findOneBy(['handle' => $handle]);
        if ($existingChannel) {
            $existingChannel->setIsSubscribed(true);
            $em->flush();
            return new JsonResponse(['message' => 'This channel is already registered, but you have now subscribed.'], JsonResponse::HTTP_OK);
        }

        /** @var ChannelDTO */
        $channelData = $youTubeApiService->fetchChannelDetails($handle);
        if (!$channelData) {
            return new JsonResponse(['error' => 'Unable to retrieve channel data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $channel = new Channel();
        $channel->setHandle($channelData->handle);
        $channel->setYoutubeId($channelData->id);
        $channel->setTitle($channelData->title);
        $channel->setThumbnail($channelData->thumbnail);
        $channel->setIsSubscribed(true);

        $em->persist($channel);
        $em->flush();

        return new JsonResponse(['message' => 'YouTube channel successfully added'], JsonResponse::HTTP_CREATED);
    }
}
