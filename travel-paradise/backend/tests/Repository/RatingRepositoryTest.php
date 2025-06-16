<?php

namespace App\Tests\Repository;

use App\Entity\Rating;
use App\Entity\User;
use App\Entity\Visit;
use App\Repository\RatingRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RatingRepositoryTest extends KernelTestCase
{
    private RatingRepository $ratingRepository;
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->ratingRepository = $this->entityManager->getRepository(Rating::class);
    }

    public function testFindByVisit(): void
    {
        // Créer une visite de test
        $visit = new Visit();
        $this->entityManager->persist($visit);
        $this->entityManager->flush();

        // Créer des notes de test
        for ($i = 0; $i < 15; $i++) {
            $rating = new Rating();
            $rating->setVisit($visit);
            $rating->setRating(4);
            $rating->setComment('Test comment ' . $i);
            $rating->setStatus('approved');
            $rating->setCreatedAt(new \DateTime());
            $this->entityManager->persist($rating);
        }
        $this->entityManager->flush();

        // Tester la pagination
        $ratings = $this->ratingRepository->findByVisit($visit->getId(), 1, 10);
        $this->assertCount(10, $ratings);

        $ratings = $this->ratingRepository->findByVisit($visit->getId(), 2, 10);
        $this->assertCount(5, $ratings);
    }

    public function testFindPendingModeration(): void
    {
        // Créer des notes en attente
        for ($i = 0; $i < 5; $i++) {
            $rating = new Rating();
            $rating->setRating(4);
            $rating->setComment('Pending comment ' . $i);
            $rating->setStatus('pending');
            $rating->setCreatedAt(new \DateTime());
            $this->entityManager->persist($rating);
        }
        $this->entityManager->flush();

        $pendingRatings = $this->ratingRepository->findPendingModeration();
        $this->assertCount(5, $pendingRatings);
    }

    public function testFindFlagged(): void
    {
        // Créer des notes signalées
        for ($i = 0; $i < 3; $i++) {
            $rating = new Rating();
            $rating->setRating(4);
            $rating->setComment('Flagged comment ' . $i);
            $rating->setStatus('flagged');
            $rating->setCreatedAt(new \DateTime());
            $this->entityManager->persist($rating);
        }
        $this->entityManager->flush();

        $flaggedRatings = $this->ratingRepository->findFlagged();
        $this->assertCount(3, $flaggedRatings);
    }

    public function testGetAverageRatingForVisit(): void
    {
        // Créer une visite de test
        $visit = new Visit();
        $this->entityManager->persist($visit);
        $this->entityManager->flush();

        // Créer des notes avec différentes valeurs
        $ratings = [5, 4, 3, 5, 4];
        foreach ($ratings as $value) {
            $rating = new Rating();
            $rating->setVisit($visit);
            $rating->setRating($value);
            $rating->setComment('Test comment');
            $rating->setStatus('approved');
            $rating->setCreatedAt(new \DateTime());
            $this->entityManager->persist($rating);
        }
        $this->entityManager->flush();

        $average = $this->ratingRepository->getAverageRatingForVisit($visit->getId());
        $this->assertEquals(4.2, $average);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
} 