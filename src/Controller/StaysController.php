<?php
// REGISTER NEW USER PATIENT BY API 

namespace App\Controller;

use App\Entity\Patient;
use App\Repository\StayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;


class StaysController extends AbstractController
{
    // GET USER AUTH STAYS
    #[Route('/api/stays', name: 'getStays', methods: ['GET'])]
    public function getStays(StayRepository $stayRepository, SerializerInterface $serializer): JsonResponse
    {   
        // Retrieve the user and the patient from Token
        $user = $this->getUser();
        $patient = $user->getPatient();

        $staysList = $stayRepository->findStaysByPatient($patient);
        $jsonStaysList = $serializer->serialize($staysList, 'json', ['groups' => 'getStays']);

        return new JsonResponse($jsonStaysList, Response::HTTP_OK, [], true);        
    }

    // GET STAYS BY PATIENT
    #[Route('/api/stays/{id}', name: 'getStaysByPatient', methods: ['GET'])]
    public function getStaysByPAtient(Patient $patient, StayRepository $stayRepository, SerializerInterface $serializer): JsonResponse
    {   
        // Retrieve the user from the token
        $user = $this->getUser();
        
        $staysList = $stayRepository->findStaysByPatient($patient);
        $jsonStaysList = $serializer->serialize($staysList, 'json');

        return new JsonResponse($jsonStaysList, Response::HTTP_OK, [], true);        
    }
}
