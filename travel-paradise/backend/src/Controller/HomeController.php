<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        $response = new Response(
            json_encode([
                'message' => 'Welcome to TravelParadise API',
                'version' => '1.0.0',
                'status' => 'active'
            ]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
        
        $response->setCache([
            'public' => true,
            'max_age' => 3600,
            's_maxage' => 3600
        ]);
        
        return $response;
    }
} 