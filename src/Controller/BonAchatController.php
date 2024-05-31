<?php

namespace App\Controller;

use App\Entity\BonAchat;
use App\Services\BonAchatService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class BonAchatController extends AbstractController
{

    #[Route( '/api/bons-achat', name: 'create_bon_achat', methods: ['POST'])]
    public function createBonAchat(Request $request, BonAchatService $bonAchatService, SerializerInterface $serializer): Response
    {
        $data = json_decode($request->getContent(), true);
        $bonAchat = $bonAchatService->CreateBonAchat($data);
        $jsonContent = $serializer->serialize($bonAchat, 'json', ['groups' => 'bon_achat']);
        return new JsonResponse($jsonContent, Response::HTTP_CREATED, [], true);
    }

    #[Route( '/api/bons-achat/{id}', name: 'get_bon_achat', methods: ['GET'])]
    public function getBonAchat($id, EntityManagerInterface $em, SerializerInterface $serializer): Response
    {
        $bonAchat = $em->getRepository(BonAchat::class)->find($id);
        if (!$bonAchat) {
            return new JsonResponse(['message' => 'Bon d\'Achat non trouvé'], Response::HTTP_NOT_FOUND);
        }
        // Sérialisation du bon d'achat
        $jsonContent = $serializer->serialize($bonAchat, 'json', [
            DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s',
            'groups' => 'read:bon_achat'
        ]);
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

}