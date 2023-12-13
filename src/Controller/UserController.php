<?php
// REGISTER NEW USER PATIENT BY API 

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\User;
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
        // Vérification de la desérialisation
        try {
            $userData = json_decode($request->getContent(), true);

            // Extraction de l'adresse du tableau associé aux données de l'utilisateur
            $address = $userData['address'] ?? null;
            unset($userData['address']); // Suppression de l'adresse du tableau des données de l'utilisateur

            $user = $serializer->deserialize(json_encode($userData), User::class, 'json');
            
        } catch (NotEncodableValueException $e) {
            return new JsonResponse(['error' => 'Erreur de désérialisation : ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }        

        // Validation des données
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        // Mot de passe brut
        $plaintextPassword = $user->getPassword();
        
        // Hasher le mot de passe
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        $user->setRoles(['ROLE_USER']);
        
        // enregistrement du user
        try {
            $em->persist($user);
            $em->flush();

            // création du patient associé au user            
            $patient = new Patient();
            $patient->setUser($user);

            // Ajout de l'adresse au patient
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
}
