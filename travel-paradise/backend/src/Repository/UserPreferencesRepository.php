<?php

namespace App\Repository;

use App\Entity\UserPreferences;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserPreferencesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPreferences::class);
    }

    public function findUsersWithNotificationEnabled(string $type): array
    {
        return $this->createQueryBuilder('up')
            ->andWhere('JSON_CONTAINS(up.notificationPreferences, :type) = 1')
            ->setParameter('type', json_encode($type))
            ->getQuery()
            ->getResult();
    }

    public function findUsersByLanguage(string $language): array
    {
        return $this->createQueryBuilder('up')
            ->andWhere('up.languagePreferences LIKE :language')
            ->setParameter('language', '%' . $language . '%')
            ->getQuery()
            ->getResult();
    }

    public function findUsersByTimezone(string $timezone): array
    {
        return $this->createQueryBuilder('up')
            ->andWhere('up.displayPreferences LIKE :timezone')
            ->setParameter('timezone', '%' . $timezone . '%')
            ->getQuery()
            ->getResult();
    }
} 