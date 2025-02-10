<?php

namespace App\Repository;

use App\Entity\Channel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for managing Channel entity.
 * 
 * @extends ServiceEntityRepository<Channel>
 */
class ChannelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Channel::class);
    }

    /**
     * Retrieves channels that need to be updated.
     * 
     * A channel is considered outdated if:
     * - `updatedAt` is NULL
     * - OR `updatedAt` is older than the current date/time
     * 
     * @return Channel[] List of channels to be updated.
     */
    public function findChannelsToUpdate(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.updatedAt IS NULL OR c.updatedAt <= :currentDate')
            ->andWhere('c.isSubscribed = true')
            ->setParameter('currentDate', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }
}
