<?php
// REGISTER NEW USER PATIENT BY API 

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

    // GET STAYS BY DOCTOR
    #[Route('/api/stays/doctor/{id}', name: 'getStaysByDoctor', methods: ['GET'])]
    public function getStaysByDoctor(Doctor $doctor, StayRepository $stayRepository, SerializerInterface $serializer): JsonResponse
    { 
        $staysList = $stayRepository->findStaysByDoctor($doctor);
        $overlappingDays = $this->overlappingDays($staysList);
        $jsonResult = $serializer->serialize($overlappingDays, 'json');
        //$jsonStaysList = $serializer->serialize($staysList, 'json', ['groups' => 'getStays']);

        return new JsonResponse($jsonResult, Response::HTTP_OK, [], true);        
    }

    // POST NEW STAY
    #[Route('/api/stay', name: 'createStay', methods: ['POST'])]
    public function cretateUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        // Vérification de la desérialisation
        try {            

            //$stayData = $serializer->deserialize($request->getContent(), Stay::class, 'json'); tellement plus simple mais provoque des erreurs que je n'arrive pas à corriger
            //dump($stayData);
            $jsonData = $request->getContent();
            $dto = $serializer->deserialize($jsonData, StayDto::class, 'json');
            
            
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
        
        // enregistrement du séjour
        try {
            // Préparation des data
            $user = $this->getUser();
            $patient = $user->getPatient();
            $entranceDate = new \DateTime($dto->entranceDate);
            $dischergeDate = new \DateTime($dto->dischargeDate);
            $doctor = $em->getRepository(Doctor::class)->find($dto->doctor);            

            // Création du nouveau séjour
            $stay = new Stay();
            $stay->setPatient($patient);
            $stay->setReason($dto->reason);
            $stay->setEntranceDate($entranceDate);
            $stay->setDischargeDate($dischergeDate);
            $stay->setDoctor($doctor);            
            $stay->setService($doctor->getService());

            //dump($stay);
            $em->persist($stay);
            $em->flush();

            return new JsonResponse(['message' => 'Séjour créé avec succès.'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    // Retourne une liste de jours ou le max de séjours est atteint
    private function overlappingDays($stays): array
    {
        // Récupérer la variable MAX_STAYS_PER_DAY à partir du conteneur de services
        $maxStaysPerDay = $_ENV['MAX_STAYS_PER_DAY'] ?? null;

        // Créer un tableau pour stocker le nombre de séjours par jour
        $stayCountByDay = [];

        // Parcourir la liste des séjours
        foreach ($stays as $stay) {
            $entranceDate = $stay->getEntranceDate()->format('Y-m-d');
            $dischargeDate = $stay->getDischargeDate()->format('Y-m-d');

            // Incrémenter le nombre de séjours pour chaque jour entre entranceDate et dischargeDate
            $currentDate = new \DateTime($entranceDate);
            while ($currentDate <= $stay->getDischargeDate()) {
                $currentDateString = $currentDate->format('Y-m-d');
                $stayCountByDay[$currentDateString] = ($stayCountByDay[$currentDateString] ?? 0) + 1;
                $currentDate->modify('+1 day');
            }
        }

        // Filtrer les jours où le nombre de séjours est de 5 ou plus
        $overlappingDays = array_filter($stayCountByDay, function ($count) use ($maxStaysPerDay) {
            return $count >= $maxStaysPerDay;
        });

        // Retourner la liste des jours où la limite est atteinte ou dépassée
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