<?php
// REGISTER NEW USER PATIENT BY API 

namespace App\Controller;

use App\Entity\Patient;
use App\Repository\PrescriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class PrescriptionController extends AbstractController
{
    // GET PRESCRIPTIONS FOR ONE PATIENT
    #[Route('/api/prescriptions/{id}', name: 'getPrescriptions', methods: ['GET'])]
    public function getPrescriptions(Patient $patient, PrescriptionRepository $prescriptionRepository, SerializerInterface $serializer): JsonResponse
    {
        $prescriptions = $prescriptionRepository->findPrescriptionsByPatient($patient);

        if (!$prescriptions) { // Si il n'y a pas de prescription
            return new JsonResponse(['error' => 'Pas de prescription.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $jsonPrescriptions = $serializer->serialize($prescriptions, 'json', ['groups' => 'getPrescriptions']);

        return new JsonResponse($jsonPrescriptions, Response::HTTP_OK, [], true);        
    }    
}