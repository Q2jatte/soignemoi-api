<?php
// STAYS CONTROLLER

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Service;
use App\Entity\Stay;
use App\Entity\User;
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

class StayController extends AbstractController
{
    // ONE GET FOR ALL CASE - TODO REMPLACER toutes les méthodes getStays par celle-ci
    #[Route('/api/patients/{id}/stays/{status}', name: 'getStaysByPatientAndStatus', methods: ['GET'])]
    public function getStaysByPatientAndStatus(Patient $patient, $status, StayRepository $stayRepository, SerializerInterface $serializer): JsonResponse
    {
        // Check the status to ensure it is valid (current, old, future, or all)
        $validStatuses = ['current', 'old', 'future', 'all'];
        if (!in_array($status, $validStatuses)) {
            return new JsonResponse(['error' => 'Statut de séjour invalide.'], Response::HTTP_BAD_REQUEST);
        }

        // Get stays based on patient and status 
        if (!$patient) {
            return new JsonResponse(['error' => 'Patient non trouvé.'], Response::HTTP_NOT_FOUND);
        }
        
        $stays = $stayRepository->findStaysByPatientAndStatus($patient, $status);                
        $jsonStays = $serializer->serialize($stays, 'json', ['groups' => 'getStays']);

        return new JsonResponse($jsonStays, Response::HTTP_OK, [], true);
    }

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

    // GET ALL STAYS BY PATIENT
    #[Route('/api/stays/{id}', name: 'getStaysByPatient', methods: ['GET'])]
    public function getStaysByPAtient(Patient $patient, StayRepository $stayRepository, SerializerInterface $serializer): JsonResponse
    { 
        $staysList = $stayRepository->findStaysByPatient($patient);
        $jsonStaysList = $serializer->serialize($staysList, 'json');

        return new JsonResponse($jsonStaysList, Response::HTTP_OK, [], true);        
    }

    // GET CURENT STAY FOR ONE PATIENT
    #[Route('/api/stay/current/{id}', name: 'getCurrentStay', methods: ['GET'])]
    public function getCurrentStay(Patient $patient, StayRepository $stayRepository, SerializerInterface $serializer): JsonResponse
    {    
        $stay = $stayRepository->findCurrentStay($patient);

        if (!$stay) { // If there is no current stay
            return new JsonResponse(['error' => 'Pas de séjour en cours.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $jsonStay = $serializer->serialize($stay, 'json', ['groups' => 'getStays']);

        return new JsonResponse($jsonStay, Response::HTTP_OK, [], true);        
    }

    // GET OLD STAYS FOR ONE PATIENT
    #[Route('/api/stay/old/{id}', name: 'getOldStays', methods: ['GET'])]
    public function getOldStays(Patient $patient, StayRepository $stayRepository, SerializerInterface $serializer): JsonResponse
    {    
        $stays = $stayRepository->findOldStays($patient);

        if (!$stays) { // If there is no current stay
            return new JsonResponse(['error' => 'Pas de séjour précédent.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $jsonStays = $serializer->serialize($stays, 'json', ['groups' => 'getStays']);

        return new JsonResponse($jsonStays, Response::HTTP_OK, [], true);        
    }

    // GET UNAVAILABLE DAYS OF STAYS FOR ONE DOCTOR
    #[Route('/api/stays/doctor/{id}', name: 'getStaysByDoctor', methods: ['GET'])]
    public function getStaysByDoctor(Doctor $doctor, StayRepository $stayRepository, SerializerInterface $serializer): JsonResponse
    { 
        $staysList = $stayRepository->findStaysByDoctor($doctor);
        $overlappingDays = $this->overlappingDays($staysList);
        $jsonResult = $serializer->serialize($overlappingDays, 'json');        

        return new JsonResponse($jsonResult, Response::HTTP_OK, [], true);        
    }    

    // GET STAYS ENTRIES
    #[Route('/api/entries', name: 'getEntries', methods: ['GET'])]
    public function getEntries(StayRepository $stayRepository, SerializerInterface $serializer): JsonResponse
    { 
        $entriesList = $stayRepository->findEntries();        
        $jsonResult = $serializer->serialize($entriesList, 'json', ['groups' => 'getEntries']);

        return new JsonResponse($jsonResult, Response::HTTP_OK, [], true);        
    }

    // GET STAYS EXITS
    #[Route('/api/exits', name: 'getExits', methods: ['GET'])]
    public function getExits(StayRepository $stayRepository, SerializerInterface $serializer): JsonResponse
    { 
        $exitsList = $stayRepository->findExits();        
        $jsonResult = $serializer->serialize($exitsList, 'json', ['groups' => 'getExits']);

        return new JsonResponse($jsonResult, Response::HTTP_OK, [], true);        
    }

    // GET CURENT OCCUPATION COUNT
    #[Route('/api/stay/occupation', name: 'getOccupation', methods: ['GET'])]
    public function getOccupation(StayRepository $stayRepository, SerializerInterface $serializer): JsonResponse
    {    
        $occupation = $stayRepository->findAllCurrentStays();
        $jsonStay = $serializer->serialize($occupation, 'json');
        return new JsonResponse($jsonStay, Response::HTTP_OK, [], true);        
    }

    // POST NEW STAY
    #[Route('/api/stay', name: 'createStay', methods: ['POST'])]
    public function cretateUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        // Check for deserialization
        try {           
            $jsonData = $request->getContent();
            $dto = $serializer->deserialize($jsonData, StayDto::class, 'json');
        } catch (NotEncodableValueException | UnexpectedValueException $e) {
            return new JsonResponse(['error' => 'Erreur de désérialisation : ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }       

        // Validation of data
        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }
        
        // Save the stay
        try {
            // Prepare data
            $user = $this->getUser();
            $patient = $user->getPatient();

            // Set the time to noon (12:00:00) for entrance and discharge dates            
            $entranceDate = new \DateTime(substr($dto->entranceDate, 0, 11) . '12:00:00' . '.000Z');
            $dischargeDate = new \DateTime(substr($dto->dischargeDate, 0, 11) . '12:00:00' . '.000Z');            
            
            $doctor = $em->getRepository(Doctor::class)->find($dto->doctor);            

            // Create the new stay
            $stay = new Stay();
            $stay->setPatient($patient);
            $stay->setReason($dto->reason);
            $stay->setEntranceDate($entranceDate);
            $stay->setDischargeDate($dischargeDate);
            $stay->setDoctor($doctor);            
            $stay->setService($doctor->getService());

            $em->persist($stay);
            $em->flush();

            return new JsonResponse(['message' => 'Séjour créé avec succès.'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Business methods
    // Returns a list of days where the maximum stays is reached
    private function overlappingDays($stays): array
    {
        // Get MAX_STAYS_PER_DAY variable from the service container
        $maxStaysPerDay = $_ENV['MAX_STAYS_PER_DAY'] ?? null;

        // Create an array to store the number of stays per day
        $stayCountByDay = [];

        // Iterate through the list of stays
        foreach ($stays as $stay) {
            $entranceDate = $stay->getEntranceDate()->format('Y-m-d');
            $dischargeDate = $stay->getDischargeDate()->format('Y-m-d');

            // Increment the number of stays for each day between entranceDate and dischargeDate
            $currentDate = new \DateTime($entranceDate);
            while ($currentDate <= $stay->getDischargeDate()) {
                $currentDateString = $currentDate->format('Y-m-d');
                $stayCountByDay[$currentDateString] = ($stayCountByDay[$currentDateString] ?? 0) + 1;
                $currentDate->modify('+1 day');
            }
        }

        // Filter the days where the number of stays is 5 or more
        $overlappingDays = array_filter($stayCountByDay, function ($count) use ($maxStaysPerDay) {
            return $count >= $maxStaysPerDay;
        });

        // Return the list of days where the limit is reached or exceeded
        $result = array_keys($overlappingDays);

        return $result;
    }
}

class StayDto
{
    public $dischargeDate;
    public $doctor;
    public $entranceDate;    
    public $reason;    
}
