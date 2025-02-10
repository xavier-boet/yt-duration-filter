<?php

namespace App\Command;

use App\Repository\ChannelRepository;
use App\Repository\VideoRepository;
use App\Service\VideoService;
use App\Service\YouTubeService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:channel:refresh',
    description: 'Updates information on YouTube channels and their videos.',
)]
class ChannelUpdateCommand extends Command
{
    public function __construct(
        private ChannelRepository $channelRepository,
        private VideoRepository $videoRepository,
        private VideoService $videoService,
        private YouTubeService $youTubeService
    ) {
        parent::__construct();
    }

    // TODO
    // protected function configure(): void
    // {
    //     $this
    //         ->addArgument('channelId', InputArgument::OPTIONAL, 'YouTube Channel ID to update (if not provided, all channels will be updated)')
    //         ->addOption('force', null, InputOption::VALUE_NONE, 'Force update even if the channel is up-to-date');
    // }    

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Starting YouTube channel update process');

        $io->section('Adding new videos...');
        $this->addNewVideos($io);
        $io->success('New videos have been added.');

        $io->section('Fetching missing details for videos...');
        $this->setDetailsToNewVideos($io);
        $io->success('Video details have been updated.');

        $io->success('YouTube channel update completed successfully.');

        return Command::SUCCESS;
    }


    /**
     * Fetches new videos from YouTube channels and saves them in the database.
     * 
     * @param SymfonyStyle $io Console output handler for logging messages.
     */
    private function addNewVideos(SymfonyStyle $io): void
    {
        $channels = $this->channelRepository->findChannelsToUpdate();
        if (empty($channels)) {
            $io->warning('No channels need updates.');
            return;
        }

        foreach ($channels as $channel) {
            $io->note(sprintf('Fetching videos for channel: %s', $channel->getTitle()));
            $rssVideos = $this->youTubeService->getVideosFromChannel($channel->getYoutubeId());

            $unknownRssVideos = $this->videoService->filterNewRssVideos($rssVideos);
            $this->videoRepository->addVideos($channel, $unknownRssVideos);

            $io->success(sprintf('%d new videos added for channel: %s', count($unknownRssVideos), $channel->getTitle()));
        }
    }



    /**
     * Fetches missing details (title, channel, duration, etc.) for videos.
     *
     * @param SymfonyStyle $io Console output handler for logging messages.
     */
    private function setDetailsToNewVideos(SymfonyStyle $io): void
    {
        $entries = $this->videoRepository->getEntriesWithoutDuration(); //TODO
        if (empty($entries)) {
            $io->warning('No videos need detail updates.');
            return;
        }

        $io->note(sprintf('Updating details for %d videos...', count($entries)));
        $this->youTubeService->setVideosDetails($entries);
        $io->success('Video details updated successfully.');
    }
}
