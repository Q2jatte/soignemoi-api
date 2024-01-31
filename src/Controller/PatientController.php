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
use App\Repository\UserRepository;
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
    // GET ONE PATIENT BY ID
    #[Route('/api/patient/{id}', name: 'getPatient', methods: ['GET'])]
    public function getPatient($id, PatientRepository $patientRepository, SerializerInterface $serializer): JsonResponse
    {
        try {            
            $patient = $patientRepository->find($id);

            if (!$patient) {
                return new JsonResponse(['error' => 'Patient non trouvé.'], JsonResponse::HTTP_NOT_FOUND);
            }

            // sérialiser le patient
            $jsonPatient = $serializer->serialize($patient, 'json', ['groups' => 'getPatients']);

            return new JsonResponse($jsonPatient, JsonResponse::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur s\'est produite : ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // GET PATIENTS LIST FOR TODAY BY USER AUTH
    #[Route('/api/patients', name: 'getPatients', methods: ['GET'])]
    public function getPatients(StayRepository $stayRepository, SerializerInterface $serializer): JsonResponse
    {   
        $user = $this->getUser();
        
        try {
            $doctor = $user->getDoctor();
            $patientsList = $stayRepository->findPatientByDoctor($doctor);
            $jsonPatientsList = $serializer->serialize($patientsList, 'json', ['groups' => 'getPatients']);
            
            return new JsonResponse($jsonPatientsList, Response::HTTP_OK, [], true);   
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur s\'est produite : ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // GET DAILY VISITS FOR ALL DOCTORs
    #[Route('/api/visits/doctors', name: 'getVisitsForAllDoctors', methods: ['GET'])]
    public function getVisitsForAllDoctors(StayRepository $stayRepository, SerializerInterface $serializer): JsonResponse
    {              
        try {            
            $patientsList = $stayRepository->findPatientForAllDoctors();
            $jsonPatientsList = $serializer->serialize($patientsList, 'json', ['groups' => 'getPatients']);
            
            return new JsonResponse($jsonPatientsList, Response::HTTP_OK, [], true);   
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur s\'est produite : ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }                
    }

    // SEARCH PATIENTS
    #[Route('/api/patients/search', name: 'searchPatients', methods: ['POST'])]
    public function searchPatients(Request $request, PatientRepository $patientRepository, SerializerInterface $serializer): JsonResponse
    {   
        try {            
            $jsonData = json_decode($request->getContent(), true);            
            $query = $jsonData['query'];
            
        } catch (NotEncodableValueException | UnexpectedValueException $e) {
            return new JsonResponse(['error' => 'Erreur de désérialisation : ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }       

        try {
            
            $patientsList = $patientRepository->findPatientByName($query);            
            $jsonPatientsList = $serializer->serialize($patientsList, 'json', ['groups' => 'getPatients']);
            
            return new JsonResponse($jsonPatientsList, Response::HTTP_OK, [], true); 

        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur s\'est produite : ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}