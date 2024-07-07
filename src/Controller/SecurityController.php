<?php

namespace App\Controller;





class SecurityController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function apiLogin(): JsonResponse
    {
        $user = $this->getUser();
        return new JsonResponse([
            'email' => $user->getUsername(),
            'roles' => $user->getRoles()
        ]);

    }

    #[Route('/api/logout', name: 'logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return new JsonResponse(['message' => 'Logged out'], Response::HTTP_OK);
    }
}