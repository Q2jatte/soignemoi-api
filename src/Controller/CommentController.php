<?php
// REGISTER NEW USER PATIENT BY API 

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Repository\CommentRepository;
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

class CommentController extends AbstractController
{
    // GET COMMENTS FOR ONE PATIENT
    #[Route('/api/comments/{id}', name: 'getcomments', methods: ['GET'])]
    public function getComments(Patient $patient, CommentRepository $commentRepository, SerializerInterface $serializer): JsonResponse
    {
        $comments = $commentRepository->findCommentsByPatient($patient);

        if (!$comments) { // Si il n'y a pas de prescription
            return new JsonResponse(['error' => 'Pas de commentaire.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $jsonData = $serializer->serialize($comments, 'json', ['groups' => 'getComments']);

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);        
    }  
    
    // POST NEW COMMENT FOR ONE PATIENT
    #[Route('/api/comment/new', name: 'createComment', methods: ['POST'])]
    public function createCommentw(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        // Vérification de la desérialisation
        try {    
            $jsonData = $request->getContent();
            $dto = $serializer->deserialize($jsonData, CommentDto::class, 'json');

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
            
            $createAt = new \DateTime($dto->createAt);
            

            // on répurère le doctor correspondant au token 
            $doctor = $em->getRepository(Doctor::class)->findOneBy(['user' => $user]);  
            // et le patient avec son id
            $patient = $em->getRepository(Patient::class)->findOneBy(['id' => $dto->patient['id']]);  
            
            // comment            
            $comment = new Comment();
            $comment->setTitle($dto->title);
            $comment->setContent($dto->content);
            $comment->setCreateAt($createAt);
            $comment->setDoctor($doctor);
            $comment->setPatient($patient);
            $em->persist($comment);
            $em->flush();           

            return new JsonResponse(['message' => 'Commentaire ajouté avec succès.'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    
}

class CommentDto
{
    public $title;
    public $content;
    public $createAt;  
    public $patient;      
}