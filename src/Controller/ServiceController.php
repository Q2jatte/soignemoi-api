<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends AbstractController
{
    // GET ALL SERVICES
    #[Route('/api/services', name: 'getServices', methods: ['GET'])]
    public function getServices(ServiceRepository $serviceRepository, SerializerInterface $serializer): JsonResponse
    {
        $servicesList = $serviceRepository->findServices();
        $jsonServicesList = $serializer->serialize($servicesList, 'json', ['groups' => 'getServices']);

        return new JsonResponse($jsonServicesList, Response::HTTP_OK, [], true);        
    }
}
