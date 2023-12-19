<?php
// REGISTER NEW USER PATIENT BY API 

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Service;
use App\Entity\Stay;
use App\Entity\User;
use App\Repository\PatientRepository;
use App\Repository\StayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class PatientController extends AbstractController
{
    // GET PATIENTS LIST FOR TODAY BY USER AUTH
    #[Route('/api/patients', name: 'getPatients', methods: ['GET'])]
    public function getPatients(StayRepository $stayRepository, SerializerInterface $serializer): JsonResponse
    {   
        $user = $this->getUser();

        // Accès limité aux docteurs
        
        if (in_array("ROLE_DOCTOR", $user->getRoles())) {
            try {
                $doctor = $user->getDoctor();
                $patientsList = $stayRepository->findPatientByDoctor($doctor);
                $jsonPatientsList = $serializer->serialize($patientsList, 'json', ['groups' => 'getPatients']);
                
                return new JsonResponse($jsonPatientsList, Response::HTTP_OK, [], true);   
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Une erreur s\'est produite : ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            return new JsonResponse(['error' => 'Accès non autorisé'], Response::HTTP_FORBIDDEN);
        }
             
    }
}