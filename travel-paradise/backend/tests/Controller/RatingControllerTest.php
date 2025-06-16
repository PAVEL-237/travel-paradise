<?php

namespace App\Tests\Controller;

use App\Entity\Rating;
use App\Entity\User;
use App\Entity\Visit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RatingControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $user;
    private $admin;
    private $visit;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        // Créer un utilisateur normal
        $this->user = new User();
        $this->user->setEmail('user@test.com');
        $this->user->setPassword('password');
        $this->user->setRoles(['ROLE_USER']);
        $this->entityManager->persist($this->user);

        // Créer un administrateur
        $this->admin = new User();
        $this->admin->setEmail('admin@test.com');
        $this->admin->setPassword('password');
        $this->admin->setRoles(['ROLE_ADMIN']);
        $this->entityManager->persist($this->admin);

        // Créer une visite de test
        $this->visit = new Visit();
        $this->entityManager->persist($this->visit);

        $this->entityManager->flush();
    }

    public function testCreateRating(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('POST', '/api/ratings', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'visitId' => $this->visit->getId(),
                'rating' => 5,
                'comment' => 'Excellent service !'
            ])
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['message' => 'Rating created successfully']);
    }

    public function testCreateRatingWithInvalidData(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('POST', '/api/ratings', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'visitId' => $this->visit->getId(),
                'rating' => 6, // Invalid rating
                'comment' => 'Too short' // Invalid comment length
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testUpdateRating(): void
    {
        // Créer une note existante
        $rating = new Rating();
        $rating->setVisit($this->visit);
        $rating->setUser($this->user);
        $rating->setRating(4);
        $rating->setComment('Original comment');
        $rating->setStatus('approved');
        $rating->setCreatedAt(new \DateTime());
        $this->entityManager->persist($rating);
        $this->entityManager->flush();

        $this->client->loginUser($this->user);

        $this->client->request('PUT', '/api/ratings/' . $rating->getId(), [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'rating' => 5,
                'comment' => 'Updated comment'
            ])
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['message' => 'Rating updated successfully']);
    }

    public function testDeleteRating(): void
    {
        // Créer une note existante
        $rating = new Rating();
        $rating->setVisit($this->visit);
        $rating->setUser($this->user);
        $rating->setRating(4);
        $rating->setComment('Test comment');
        $rating->setStatus('approved');
        $rating->setCreatedAt(new \DateTime());
        $this->entityManager->persist($rating);
        $this->entityManager->flush();

        $this->client->loginUser($this->user);

        $this->client->request('DELETE', '/api/ratings/' . $rating->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testListRatingsByVisit(): void
    {
        // Créer plusieurs notes
        for ($i = 0; $i < 15; $i++) {
            $rating = new Rating();
            $rating->setVisit($this->visit);
            $rating->setUser($this->user);
            $rating->setRating(4);
            $rating->setComment('Test comment ' . $i);
            $rating->setStatus('approved');
            $rating->setCreatedAt(new \DateTime());
            $this->entityManager->persist($rating);
        }
        $this->entityManager->flush();

        $this->client->request('GET', '/api/ratings/visit/' . $this->visit->getId() . '?page=1&limit=10');

        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(10, $response['ratings']);
        $this->assertEquals(15, $response['pagination']['total']);
    }

    public function testModerateRating(): void
    {
        // Créer une note en attente
        $rating = new Rating();
        $rating->setVisit($this->visit);
        $rating->setUser($this->user);
        $rating->setRating(4);
        $rating->setComment('Test comment');
        $rating->setStatus('pending');
        $rating->setCreatedAt(new \DateTime());
        $this->entityManager->persist($rating);
        $this->entityManager->flush();

        $this->client->loginUser($this->admin);

        $this->client->request('POST', '/api/ratings/moderate/' . $rating->getId(), [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['action' => 'approve'])
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['message' => 'Rating moderated successfully']);
    }

    public function testModerateRatingAsNonAdmin(): void
    {
        // Créer une note en attente
        $rating = new Rating();
        $rating->setVisit($this->visit);
        $rating->setUser($this->user);
        $rating->setRating(4);
        $rating->setComment('Test comment');
        $rating->setStatus('pending');
        $rating->setCreatedAt(new \DateTime());
        $this->entityManager->persist($rating);
        $this->entityManager->flush();

        $this->client->loginUser($this->user);

        $this->client->request('POST', '/api/ratings/moderate/' . $rating->getId(), [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['action' => 'approve'])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
} 