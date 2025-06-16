<?php

namespace App\Entity;

use App\Repository\UserPreferencesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPreferencesRepository::class)]
class UserPreferences
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'preferences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'json')]
    private array $notificationPreferences = [
        'email' => true,
        'push' => true,
        'reminder_before_visit' => true,
        'new_places' => true,
        'special_offers' => true
    ];

    #[ORM\Column(type: 'json')]
    private array $languagePreferences = [
        'interface' => 'fr',
        'notifications' => 'fr'
    ];

    #[ORM\Column(type: 'json')]
    private array $displayPreferences = [
        'theme' => 'light',
        'currency' => 'EUR',
        'timezone' => 'Europe/Paris'
    ];

    #[ORM\ManyToMany(targetEntity: Place::class)]
    #[ORM\JoinTable(name: 'user_preferences_favorite_places',
        joinColumns: [new ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'place_id', referencedColumnName: 'id')]
    )]
    private Collection $favoritePlaces;

    #[ORM\ManyToMany(targetEntity: Place::class)]
    #[ORM\JoinTable(name: 'user_preferences_visited_places',
        joinColumns: [new ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'place_id', referencedColumnName: 'id')]
    )]
    private Collection $visitedPlaces;

    public function __construct()
    {
        $this->favoritePlaces = new ArrayCollection();
        $this->visitedPlaces = new ArrayCollection();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getNotificationPreferences(): array
    {
        return $this->notificationPreferences;
    }

    public function setNotificationPreferences(array $preferences): static
    {
        $this->notificationPreferences = $preferences;
        return $this;
    }

    public function getLanguagePreferences(): array
    {
        return $this->languagePreferences;
    }

    public function setLanguagePreferences(array $preferences): static
    {
        $this->languagePreferences = $preferences;
        return $this;
    }

    public function getDisplayPreferences(): array
    {
        return $this->displayPreferences;
    }

    public function setDisplayPreferences(array $preferences): static
    {
        $this->displayPreferences = $preferences;
        return $this;
    }

    /**
     * @return Collection<int, Place>
     */
    public function getFavoritePlaces(): Collection
    {
        return $this->favoritePlaces;
    }

    public function addFavoritePlace(Place $place): static
    {
        if (!$this->favoritePlaces->contains($place)) {
            $this->favoritePlaces->add($place);
        }
        return $this;
    }

    public function removeFavoritePlace(Place $place): static
    {
        $this->favoritePlaces->removeElement($place);
        return $this;
    }

    /**
     * @return Collection<int, Place>
     */
    public function getVisitedPlaces(): Collection
    {
        return $this->visitedPlaces;
    }

    public function addVisitedPlace(Place $place): static
    {
        if (!$this->visitedPlaces->contains($place)) {
            $this->visitedPlaces->add($place);
        }
        return $this;
    }

    public function removeVisitedPlace(Place $place): static
    {
        $this->visitedPlaces->removeElement($place);
        return $this;
    }
} 