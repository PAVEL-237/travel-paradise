<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un administrateur
        $admin = new User();
        $admin->setEmail('admin@travelparadise.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('System');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsActive(true);
        
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'admin123'
        );
        $admin->setPassword($hashedPassword);
        
        $manager->persist($admin);

        // Création d'un guide
        $guide = new User();
        $guide->setEmail('guide@travelparadise.com');
        $guide->setFirstName('Guide');
        $guide->setLastName('Test');
        $guide->setRoles(['ROLE_GUIDE']);
        $guide->setIsActive(true);
        
        $hashedPassword = $this->passwordHasher->hashPassword(
            $guide,
            'guide123'
        );
        $guide->setPassword($hashedPassword);
        
        $manager->persist($guide);

        $manager->flush();
    }
} 