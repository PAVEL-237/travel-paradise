<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager
    ) {}

    public function register(array $data): User
    {
        // Vérifier si l'email existe déjà
        if ($this->userRepository->findOneBy(['email' => $data['email']])) {
            throw new \Exception('Email already exists');
        }

        // Créer un nouvel utilisateur
        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setPhone($data['phone'] ?? null);

        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $data['password']
        );
        $user->setPassword($hashedPassword);

        // Sauvegarder l'utilisateur
        $this->userRepository->save($user, true);

        return $user;
    }

    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new AuthenticationException('Invalid credentials');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new AuthenticationException('Invalid credentials');
        }

        if (!$user->isActive()) {
            throw new AuthenticationException('Account is not active');
        }

        // Mettre à jour la date de dernière connexion
        $user->setLastLoginAt(new \DateTimeImmutable());
        $this->userRepository->save($user, true);

        // Générer le token JWT
        $token = $this->jwtManager->create($user);

        return [
            'token' => $token,
            'user' => $user
        ];
    }

    public function refreshToken(User $user): string
    {
        return $this->jwtManager->create($user);
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
            throw new AuthenticationException('Current password is invalid');
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);
        $this->userRepository->save($user, true);
    }

    public function resetPassword(string $email): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new \Exception('User not found');
        }

        // Générer un mot de passe temporaire
        $tempPassword = bin2hex(random_bytes(8));
        $hashedPassword = $this->passwordHasher->hashPassword($user, $tempPassword);
        $user->setPassword($hashedPassword);
        $this->userRepository->save($user, true);

        // TODO: Envoyer un email avec le mot de passe temporaire
    }

    public function activateAccount(string $email): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new \Exception('User not found');
        }

        $user->setIsActive(true);
        $this->userRepository->save($user, true);
    }

    public function deactivateAccount(string $email): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new \Exception('User not found');
        }

        $user->setIsActive(false);
        $this->userRepository->save($user, true);
    }
} 