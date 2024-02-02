<?php
// USERS CONTROLLER 

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class UserController extends AbstractController
{
    #[Route('/api/user/signup', name: 'createUser', methods: ['POST'])]
    public function cretateUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): JsonResponse
    {
        // Check for deserialization
        try {
            $userData = json_decode($request->getContent(), true);

            // Extract address from the array associated with user data
            $address = $userData['address'] ?? null;
            unset($userData['address']); // Remove address from the user data array
            $user = $serializer->deserialize(json_encode($userData), User::class, 'json');
            
        } catch (NotEncodableValueException $e) {
            return new JsonResponse(['error' => 'Erreur de désérialisation : ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }        

        // Validation of data
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        // Raw password
        $plaintextPassword = $user->getPassword();
        
        // Hash the password
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        $user->setRoles(['ROLE_USER']);
        
        // Save the user
        try {
            $em->persist($user);
            $em->flush();

            // Create the patient associated with the user            
            $patient = new Patient();
            $patient->setUser($user);

            // Add address to the patient
            if ($address !== null) {
                $patient->setAddress($address);
            }
            
            $em->persist($patient);
            $em->flush();

            return new JsonResponse(['message' => 'Utilisateur créé avec succès.'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }  
    }

    // Get profile information based on the user group
    #[Route('/api/user/profile', name: 'getProfile', methods: ['GET'])]
    public function getProfile(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();
        
        if ($user) {
            $id = $user->getId();
        } else {
            return new JsonResponse(['error' => 'Utilisateur non authentifié.'], Response::HTTP_BAD_REQUEST);
        }

        $profile = $userRepository->find($id);
        // Serialize data
        $jsonData = $serializer->serialize($profile, 'json', ['groups' => 'getProfile']);
        // Response
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);  
    }
}
