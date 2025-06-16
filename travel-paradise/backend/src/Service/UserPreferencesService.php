<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Place;
use App\Entity\UserPreferences;
use App\Repository\UserPreferencesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class UserPreferencesService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPreferencesRepository $preferencesRepository,
        private Security $security
    ) {}

    public function getOrCreatePreferences(User $user): UserPreferences
    {
        $preferences = $this->preferencesRepository->find($user);
        
        if (!$preferences) {
            $preferences = new UserPreferences();
            $preferences->setUser($user);
            $this->entityManager->persist($preferences);
            $this->entityManager->flush();
        }

        return $preferences;
    }

    public function updateNotificationPreferences(User $user, array $preferences): UserPreferences
    {
        $userPreferences = $this->getOrCreatePreferences($user);
        $userPreferences->setNotificationPreferences($preferences);
        $this->entityManager->flush();
        return $userPreferences;
    }

    public function updateLanguagePreferences(User $user, array $preferences): UserPreferences
    {
        $userPreferences = $this->getOrCreatePreferences($user);
        $userPreferences->setLanguagePreferences($preferences);
        $this->entityManager->flush();
        return $userPreferences;
    }

    public function updateDisplayPreferences(User $user, array $preferences): UserPreferences
    {
        $userPreferences = $this->getOrCreatePreferences($user);
        $userPreferences->setDisplayPreferences($preferences);
        $this->entityManager->flush();
        return $userPreferences;
    }

    public function addFavoritePlace(User $user, Place $place): UserPreferences
    {
        $userPreferences = $this->getOrCreatePreferences($user);
        $userPreferences->addFavoritePlace($place);
        $this->entityManager->flush();
        return $userPreferences;
    }

    public function removeFavoritePlace(User $user, Place $place): UserPreferences
    {
        $userPreferences = $this->getOrCreatePreferences($user);
        $userPreferences->removeFavoritePlace($place);
        $this->entityManager->flush();
        return $userPreferences;
    }

    public function addVisitedPlace(User $user, Place $place): UserPreferences
    {
        $userPreferences = $this->getOrCreatePreferences($user);
        $userPreferences->addVisitedPlace($place);
        $this->entityManager->flush();
        return $userPreferences;
    }

    public function getFavoritePlaces(User $user): array
    {
        $userPreferences = $this->getOrCreatePreferences($user);
        return $userPreferences->getFavoritePlaces()->toArray();
    }

    public function getVisitedPlaces(User $user): array
    {
        $userPreferences = $this->getOrCreatePreferences($user);
        return $userPreferences->getVisitedPlaces()->toArray();
    }

    public function isPlaceFavorite(User $user, Place $place): bool
    {
        $userPreferences = $this->getOrCreatePreferences($user);
        return $userPreferences->getFavoritePlaces()->contains($place);
    }

    public function isPlaceVisited(User $user, Place $place): bool
    {
        $userPreferences = $this->getOrCreatePreferences($user);
        return $userPreferences->getVisitedPlaces()->contains($place);
    }

    public function getUsersWithNotificationEnabled(string $type): array
    {
        return $this->preferencesRepository->findUsersWithNotificationEnabled($type);
    }

    public function getUsersByLanguage(string $language): array
    {
        return $this->preferencesRepository->findUsersByLanguage($language);
    }

    public function getUsersByTimezone(string $timezone): array
    {
        return $this->preferencesRepository->findUsersByTimezone($timezone);
    }
} 