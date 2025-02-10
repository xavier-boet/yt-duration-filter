<?php

namespace App\DataFixtures;

use App\Entity\Channel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ChannelFixtures extends Fixture
{
    private const CHANNELS = [
        [
            'Fireship',
            'UCsBjURrPoezykLs9EqgamOA',
            '@Fireship',
            'ytc/AIdro_mKzklyPPhghBJQH5H3HpZ108YcE618DBRLAvRUD1AjKNw=s88-c-k-c0x00ffffff-no-rj'
        ],
        [
            'Gary Clarke',
            'UCA2dkCp5DZj7HE0-6IX5ZHQ',
            '@GaryClarkeTech',
            'PkHGo7Gcl9RbKN4A-I8Ht5OX0RjQQDWU_fyzBW4au8w3-0AIZdX03llRVTXFRZr1JgyEpSLC=s88-c-k-c0xffffffff-no-rj-mo'
        ],
        [
            'Qigong Meditation',
            'UCrrG7mvA6j0Z6oGwRgLayVw',
            '@QigongMeditation',
            'ytc/AIdro_ng0j-mdcaDnUMOMcRv7fMSqe3XQMsE96uicaoa3QBQJLM=s88-c-k-c0x00ffffff-no-rj'
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CHANNELS as [$title, $youtubeId, $handle, $thumbnail]) {
            $channel = (new Channel())
                ->setTitle($title)
                ->setYoutubeId($youtubeId)
                ->setHandle($handle)
                ->setThumbnail($thumbnail)
                ->setIsSubscribed(true);

            $manager->persist($channel);
        }

        $manager->flush();
    }
}
