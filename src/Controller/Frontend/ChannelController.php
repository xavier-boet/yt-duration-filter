<?php

namespace App\Controller\Frontend;

use App\Repository\ChannelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/channels')]
final class ChannelController extends AbstractController
{
    #[Route('/', name: 'app_channel')]
    public function index(ChannelRepository $channelRepository): Response
    {
        $channels = $channelRepository->findBy([], ['title' => 'ASC']);

        return $this->render('channel/index.html.twig', [
            'channels' => $channels
        ]);
    }
}
