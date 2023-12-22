<?php
// REGISTER NEW USER PATIENT BY API 

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Medication;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Repository\PrescriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    // POST NEW PRESCRIPTION FOR ONE PATIENT
    #[Route('/api/prescription/new', name: 'createPrescription', methods: ['POST'])]
    public function createPrescription(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        // Vérification de la desérialisation
        try {    
            $jsonData = $request->getContent();
            $dto = $serializer->deserialize($jsonData, PrescriptionDto::class, 'json');
            
        } catch (NotEncodableValueException | UnexpectedValueException $e) {
            return new JsonResponse(['error' => 'Erreur de désérialisation : ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }   
        
        // Validation des données
        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Préparation des data
            $user = $this->getUser();
            
            $startAt = new \DateTime($dto->startAt);
            $endAt = new \DateTime($dto->endAt);

            // on répurère le doctor correspondant au token 
            $doctor = $em->getRepository(Doctor::class)->findOneBy(['user' => $user]);  
            // et le patient avec son id
            $patient = $em->getRepository(Patient::class)->findOneBy(['id' => $dto->patient['id']]);  
            
            // Tableau des médications
            $medications = $dto->medications;
            
            $prescription = new Prescription();
            $prescription->setStartAt($startAt);
            $prescription->setEndAt($endAt);
            $prescription->setDoctor($doctor);
            $prescription->setPatient($patient);
            $em->persist($prescription);
            $em->flush();

            // Boucle pour ajouter toutes les médications
            
            foreach ($medications as $row) {
                $medication = new Medication();
                $medication->setName($row['name']);
                $medication->setDosage($row['dosage']);
                $medication->setPrescription($prescription);
                $em->persist($medication);
                $em->flush();
            }

            return new JsonResponse(['message' => 'Prescription ajouté avec succès.'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    
}

class PrescriptionDto
{
    public $patient;
    public $startAt;
    public $endAt;    
    public $medications;    
}