<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\VisitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['visit:read']]),
        new GetCollection(normalizationContext: ['groups' => ['visit:read']]),
        new Post(denormalizationContext: ['groups' => ['visit:write']]),
        new Put(denormalizationContext: ['groups' => ['visit:write']]),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: VisitRepository::class)]
class Visit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['visit:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['visit:read', 'visit:write'])]
    private ?string $photo = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['visit:read', 'visit:write'])]
    private ?string $country = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['visit:read', 'visit:write'])]
    private ?string $location = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    #[Groups(['visit:read', 'visit:write'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank]
    #[Groups(['visit:read', 'visit:write'])]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['visit:read', 'visit:write'])]
    private ?int $duration = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['visit:read'])]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\ManyToOne(inversedBy: 'visits')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['visit:read', 'visit:write'])]
    private ?Guide $guide = null;

    #[ORM\ManyToOne(inversedBy: 'visits')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['visit:read', 'visit:write'])]
    private ?Place $place = null;

    #[ORM\OneToMany(mappedBy: 'visit', targetEntity: Tourist::class)]
    #[Groups(['visit:read'])]
    private Collection $tourists;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['visit:read', 'visit:write'])]
    private ?string $generalComment = null;

    #[ORM\Column]
    #[Groups(['visit:read', 'visit:write'])]
    private ?bool $isFinished = false;

    #[ORM\OneToMany(mappedBy: 'visit', targetEntity: Rating::class, orphanRemoval: true)]
    private Collection $ratings;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(['scheduled', 'in_progress', 'completed', 'cancelled'])]
    #[Groups(['visit:read', 'visit:write'])]
    private ?string $status = 'scheduled';

    public function __construct()
    {
        $this->tourists = new ArrayCollection();
        $this->ratings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;
        $this->calculateEndTime();
        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;
        $this->calculateEndTime();
        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    private function calculateEndTime(): void
    {
        if ($this->startTime && $this->duration) {
            $endTime = clone $this->startTime;
            $endTime->modify("+{$this->duration} minutes");
            $this->endTime = $endTime;
        }
    }

    public function getGuide(): ?Guide
    {
        return $this->guide;
    }

    public function setGuide(?Guide $guide): static
    {
        $this->guide = $guide;
        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): static
    {
        $this->place = $place;
        return $this;
    }

    /**
     * @return Collection<int, Tourist>
     */
    public function getTourists(): Collection
    {
        return $this->tourists;
    }

    public function addTourist(Tourist $tourist): static
    {
        if (!$this->tourists->contains($tourist)) {
            $this->tourists->add($tourist);
            $tourist->setVisit($this);
        }

        return $this;
    }

    public function removeTourist(Tourist $tourist): static
    {
        if ($this->tourists->removeElement($tourist)) {
            // set the owning side to null (unless already changed)
            if ($tourist->getVisit() === $this) {
                $tourist->setVisit(null);
            }
        }

        return $this;
    }

    public function getGeneralComment(): ?string
    {
        return $this->generalComment;
    }

    public function setGeneralComment(?string $generalComment): static
    {
        $this->generalComment = $generalComment;
        return $this;
    }

    public function isFinished(): ?bool
    {
        return $this->isFinished;
    }

    public function setIsFinished(bool $isFinished): static
    {
        $this->isFinished = $isFinished;
        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): static
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setVisit($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): static
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getVisit() === $this) {
                $rating->setVisit(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }
} 