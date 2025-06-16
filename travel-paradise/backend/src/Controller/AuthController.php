<?php

namespace App\Controller;

use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        private AuthService $authService,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('/register', name: 'app_auth_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $user = $this->authService->register($data);
            return $this->json([
                'message' => 'User registered successfully',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/login', name: 'app_auth_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $result = $this->authService->login($data['email'], $data['password']);
            return $this->json($result);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 401);
        }
    }

    #[Route('/refresh-token', name: 'app_auth_refresh_token', methods: ['POST'])]
    public function refreshToken(): JsonResponse
    {
        $user = $this->getUser();
        $token = $this->authService->refreshToken($user);

        return $this->json([
            'token' => $token
        ]);
    }

    #[Route('/change-password', name: 'app_auth_change_password', methods: ['POST'])]
    public function changePassword(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        try {
            $this->authService->changePassword(
                $user,
                $data['currentPassword'],
                $data['newPassword']
            );
            return $this->json([
                'message' => 'Password changed successfully'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/reset-password', name: 'app_auth_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->authService->resetPassword($data['email']);
            return $this->json([
                'message' => 'Password reset instructions sent to your email'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/activate-account', name: 'app_auth_activate_account', methods: ['POST'])]
    public function activateAccount(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->authService->activateAccount($data['email']);
            return $this->json([
                'message' => 'Account activated successfully'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/deactivate-account', name: 'app_auth_deactivate_account', methods: ['POST'])]
    public function deactivateAccount(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->authService->deactivateAccount($data['email']);
            return $this->json([
                'message' => 'Account deactivated successfully'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
} 