<?php

namespace App\DataFixtures;

use App\Entity\Channel;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VideoFixtures extends Fixture implements DependentFixtureInterface
{

    public static function getVideos(): array
    {
        return
            [
                // @Fireship
                [
                    '@Fireship',
                    'hdHjjBS4cs8',
                    'Brainf**k in 100 Seconds',
                    2,
                    new \DateTimeImmutable('2021-12-10')
                ],
                [
                    '@Fireship',
                    'pEfrdAtAmqk',
                    'God-Tier Developer Roadmap',
                    16,
                    new \DateTimeImmutable('2022-08-24')
                ],
                [
                    '@Fireship',
                    'ky5ZB-mqZKM',
                    'AI influencers are getting filthy rich... let\'s build one',
                    4,
                    new \DateTimeImmutable('2023-11-29')
                ],

                // @GaryClarkeTech
                [
                    '@GaryClarkeTech',
                    'uUlLAfN3rJc',
                    'Learn Object Oriented PHP - 3 Hour PHP OOP Course',
                    180,
                    new \DateTimeImmutable('2022-05-23')
                ],
                [
                    '@GaryClarkeTech',
                    'kkU43JdJQBE',
                    'Testing PHP - Up and running with PHPUnit',
                    30,
                    new \DateTimeImmutable('2021-05-10')
                ],
                [
                    '@GaryClarkeTech',
                    'pZv93AEJhS8',
                    'Create a Microservice with Symfony 6 (Full 5 Hour Course)',
                    279,
                    new \DateTimeImmutable('2022-07-13')
                ],

                // @QigongMeditation
                [
                    '@QigongMeditation',
                    'y2RAEnWreoE',
                    'Shaolin Qigong 15 Minute Daily Routine',
                    15,
                    new \DateTimeImmutable('2020-07-26')
                ],
                [
                    '@QigongMeditation',
                    'NRBOT4MnPWo',
                    'PRESS THESE 3 POINTS DAILY for A Healthy Life | Qigong Basic Acupressure Daily ( 4K Close Up)',
                    12,
                    new \DateTimeImmutable('2022-08-30')
                ],
                [
                    '@QigongMeditation',
                    'kz6xb0HLDp8',
                    'PRESS THESE 3 POINTS DAILY For A Healthy and Happy Life | Qigong Basic Acupressure Daily',
                    9,
                    new \DateTimeImmutable('2022-04-09')
                ],
            ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getVideos() as [$handle, $youtubeId, $title, $duration, $publishedAt]) {
            $channel = $manager->getRepository(Channel::class)->findOneBy(['handle' => $handle]);

            if (!$channel) {
                throw new \RuntimeException(sprintf('Channel with handle "%s" not found.', $handle));
            }

            $channel = (new Video())
                ->setChannel($channel)
                ->setYoutubeId($youtubeId)
                ->setTitle($title)
                ->setDuration($duration)
                ->setPublishedAt($publishedAt);

            $manager->persist($channel);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ChannelFixtures::class];
    }
}
