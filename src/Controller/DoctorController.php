<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\DoctorRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class DoctorController extends AbstractController
{
    // GET DOCTORS BY SERVICE
    #[Route('/api/doctors/{id}', name: 'getDoctors', methods: ['GET'])]
    public function getServices(Service $service, DoctorRepository $doctorRepository, SerializerInterface $serializer): JsonResponse
    {
        $doctorsList = $doctorRepository->findDoctorsByService($service);
        $jsonDoctorsList = $serializer->serialize($doctorsList, 'json', ['groups' => 'getDoctor']);

        return new JsonResponse($jsonDoctorsList, Response::HTTP_OK, [], true);        
    }
}
