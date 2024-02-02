<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Doctor;
use App\Entity\Medication;
use App\Entity\Patient;
use App\Entity\Prescription;
use App\Entity\Service;
use App\Entity\Staff;
use App\Entity\Stay;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use DateTime;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;
    
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    // Création d'un jeu de données
    public function load(ObjectManager $manager): void
    {
        // Création d'un user admin
        $userAdmin = new User();
        $userAdmin->setEmail("admin@test.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $manager->persist($userAdmin);
        
        // Création des services        
        $service1 = new Service();
        $service1->setName("Cardiologie");
        $manager->persist($service1);

        $service2 = new Service();
        $service2->setName("Pédiatrie");
        $manager->persist($service2);

        $service3 = new Service();
        $service3->setName("Chirurgie générale");
        $manager->persist($service3);

        $service4 = new Service();
        $service4->setName("Orthopédie");
        $manager->persist($service4);

        $service5 = new Service();
        $service5->setName("Gynécologie");
        $manager->persist($service5);        

        // Création des users
        $user1 = new User(); // USER
        $user1->setEmail("j.dujardin@test.com");
        $user1->setRoles(["ROLE_USER"]);        
        $user1->setFirstName("Jean");
        $user1->setLastName("Dujardin");
        $user1->setProfileImageName("001.png");
        $user1->setPassword($this->userPasswordHasher->hashPassword($user1, "password"));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail("jp.bacri@test.com");
        $user2->setRoles(["ROLE_USER"]);
        $user2->setFirstName("Jean-Pierre");
        $user2->setLastName("Bacri");
        $user2->setProfileImageName("002.png");
        $user2->setPassword($this->userPasswordHasher->hashPassword($user2, "password"));
        $manager->persist($user2);

        $user3 = new User();
        $user3->setEmail("j.demeaux@test.com");
        $user3->setRoles(["ROLE_USER"]);
        $user3->setFirstName("Joséphine");
        $user3->setLastName("De Meaux");
        $user3->setProfileImageName("003.png");
        $user3->setPassword($this->userPasswordHasher->hashPassword($user3, "password"));
        $manager->persist($user3);

        $user4 = new User();
        $user4->setEmail("m.berry@test.com");
        $user4->setRoles(["ROLE_USER"]);
        $user4->setFirstName("Marilou");
        $user4->setLastName("Berry");
        $user4->setProfileImageName("004.png");
        $user4->setPassword($this->userPasswordHasher->hashPassword($user4, "password"));
        $manager->persist($user4);

        $user5 = new User();
        $user5->setEmail("a.lim@soignemoi.com");
        $user5->setRoles(["ROLE_DOCTOR"]);
        $user5->setFirstName("Audrey");
        $user5->setLastName("Lim");
        $user5->setProfileImageName("005.png");
        $user5->setPassword($this->userPasswordHasher->hashPassword($user5, "password"));
        $manager->persist($user5);

        $user6 = new User();
        $user6->setEmail("m.reznick@soignemoi.com");
        $user6->setRoles(["ROLE_DOCTOR"]);
        $user6->setFirstName("Morgan");
        $user6->setLastName("Reznick");
        $user6->setProfileImageName("006.png");
        $user6->setPassword($this->userPasswordHasher->hashPassword($user6, "password"));
        $manager->persist($user6);

        $user7 = new User();
        $user7->setEmail("c.brown@soignemoi.com");
        $user7->setRoles(["ROLE_DOCTOR"]);
        $user7->setFirstName("Claire");
        $user7->setLastName("Brown");
        $user7->setProfileImageName("007.png");
        $user7->setPassword($this->userPasswordHasher->hashPassword($user7, "password"));
        $manager->persist($user7);

        $user8 = new User();
        $user8->setEmail("s.murphy@soignemoi.com");
        $user8->setRoles(["ROLE_DOCTOR"]);
        $user8->setFirstName("Shaun");
        $user8->setLastName("Murphy");
        $user8->setProfileImageName("008.png");
        $user8->setPassword($this->userPasswordHasher->hashPassword($user8, "password"));
        $manager->persist($user8);

        $user9 = new User();
        $user9->setEmail("a.park@soignemoi.com");
        $user9->setRoles(["ROLE_DOCTOR"]);
        $user9->setFirstName("Alex");
        $user9->setLastName("Park");
        $user9->setProfileImageName("009.png");
        $user9->setPassword($this->userPasswordHasher->hashPassword($user9, "password"));
        $manager->persist($user9);

        $user10 = new User();
        $user10->setEmail("n.melendez@soignemoi.com");
        $user10->setRoles(["ROLE_DOCTOR"]);
        $user10->setFirstName("Neil");
        $user10->setLastName("Melendez");
        $user10->setProfileImageName("010.png");
        $user10->setPassword($this->userPasswordHasher->hashPassword($user10, "password"));
        $manager->persist($user10);

        $user11 = new User();
        $user11->setEmail("j.dridi@soignemoi.com");
        $user11->setRoles(["ROLE_STAFF"]);
        $user11->setFirstName("Jamel");
        $user11->setLastName("Dridi");
        $user11->setProfileImageName("011.png");
        $user11->setPassword($this->userPasswordHasher->hashPassword($user11, "password"));
        $manager->persist($user11);

        $user12 = new User();
        $user12->setEmail("c.saulnier8@soignemoi.com");
        $user12->setRoles(["ROLE_STAFF"]);
        $user12->setFirstName("Clara");
        $user12->setLastName("Saulnier");
        $user12->setProfileImageName("012.png");
        $user12->setPassword($this->userPasswordHasher->hashPassword($user12, "password"));
        $manager->persist($user12);

        // Création des patients
        $patient1 = new Patient(); // PATIENT
        $patient1->setAddress("Place des Lilas 59000 Lille");
        $patient1->setUser($user1);
        $manager->persist($patient1);

        $patient2 = new Patient();
        $patient2->setAddress("Rue de la Paix 59100 Roubaix");
        $patient2->setUser($user2);
        $manager->persist($patient2);

        $patient3 = new Patient();
        $patient3->setAddress("Avenue des Roses 59200 Tourcoing");
        $patient3->setUser($user3);
        $manager->persist($patient3);

        $patient4 = new Patient();
        $patient4->setAddress("Boulevard du Nord 59400 Cambrai");
        $patient4->setUser($user4);
        $manager->persist($patient4);

        // Création des docteurs
        $doctor1 = new Doctor();
        $doctor1->setRegistrationNumber("00052");
        $doctor1->setUser($user5);
        $doctor1->setService($service1);

        $doctor2 = new Doctor();
        $doctor2->setRegistrationNumber("00123");
        $doctor2->setUser($user6);
        $doctor2->setService($service2);
        $manager->persist($doctor2);

        $doctor3 = new Doctor();
        $doctor3->setRegistrationNumber("00567");
        $doctor3->setUser($user7);
        $doctor3->setService($service3);
        $manager->persist($doctor3);

        $doctor4 = new Doctor();
        $doctor4->setRegistrationNumber("00987");
        $doctor4->setUser($user8);
        $doctor4->setService($service4);
        $manager->persist($doctor4);

        $doctor5 = new Doctor();
        $doctor5->setRegistrationNumber("00432");
        $doctor5->setUser($user9);
        $doctor5->setService($service5);
        $manager->persist($doctor5);

        $doctor6 = new Doctor();
        $doctor6->setRegistrationNumber("00234");
        $doctor6->setUser($user10);
        $doctor6->setService($service1);
        $manager->persist($doctor6);

        // Création du staff
        $staff1 = new Staff();
        $staff1->setPosition("Agent d'accueil");
        $staff1->setUser($user11);    
        $manager->persist($staff1);

        $staff2 = new Staff();
        $staff2->setPosition("Agent d'accueil");
        $staff2->setUser($user12);    
        $manager->persist($staff2);

        // Création des séjours
        $stay1Patient1 = new Stay();
        $stay1Patient1->setEntranceDate(new DateTime());
        $stay1Patient1->setDischargeDate(new DateTime('+5 days'));
        $stay1Patient1->setreason("Raison du séjour");
        $stay1Patient1->setPatient($patient1);
        $stay1Patient1->setService($service1);
        $stay1Patient1->setDoctor($doctor1);
        $manager->persist($stay1Patient1);
        
        $stay2Patient1 = new Stay();
        $stay2Patient1->setEntranceDate(new DateTime('-10 days'));
        $stay2Patient1->setDischargeDate(new DateTime('-5 days'));
        $stay2Patient1->setreason("Raison du séjour");
        $stay2Patient1->setPatient($patient1);
        $stay2Patient1->setService($service2);
        $stay2Patient1->setDoctor($doctor2);
        $manager->persist($stay2Patient1);

        $stay1Patient2 = new Stay();
        $stay1Patient2->setEntranceDate(new DateTime());
        $stay1Patient2->setDischargeDate(new DateTime('+5 days'));
        $stay1Patient2->setreason("Raison du séjour");
        $stay1Patient2->setPatient($patient2);
        $stay1Patient2->setService($service1);
        $stay1Patient2->setDoctor($doctor1);
        $manager->persist($stay1Patient2);

        $stay2Patient2 = new Stay();
        $stay2Patient2->setEntranceDate(new DateTime('-10 days'));
        $stay2Patient2->setDischargeDate(new DateTime('-5 days'));
        $stay2Patient2->setreason("Raison du séjour");
        $stay2Patient2->setPatient($patient2);
        $stay2Patient2->setService($service2);
        $stay2Patient2->setDoctor($doctor2);
        $manager->persist($stay2Patient2);
        
        $stay1Patient3 = new Stay();
        $stay1Patient3->setEntranceDate(new DateTime());
        $stay1Patient3->setDischargeDate(new DateTime('+5 days'));
        $stay1Patient3->setreason("Raison du séjour");
        $stay1Patient3->setPatient($patient3);
        $stay1Patient3->setService($service1);
        $stay1Patient3->setDoctor($doctor1);
        $manager->persist($stay1Patient3);

        $stay2Patient3 = new Stay();
        $stay2Patient3->setEntranceDate(new DateTime('-10 days'));
        $stay2Patient3->setDischargeDate(new DateTime('-5 days'));
        $stay2Patient3->setreason("Raison du séjour");
        $stay2Patient3->setPatient($patient3);
        $stay2Patient3->setService($service2);
        $stay2Patient3->setDoctor($doctor2);
        $manager->persist($stay2Patient3);

        $stay1Patient4 = new Stay();
        $stay1Patient4->setEntranceDate(new DateTime());
        $stay1Patient4->setDischargeDate(new DateTime('+5 days'));
        $stay1Patient4->setreason("Raison du séjour");
        $stay1Patient4->setPatient($patient4);
        $stay1Patient4->setService($service1);
        $stay1Patient4->setDoctor($doctor1);
        $manager->persist($stay1Patient4);

        $stay2Patient4 = new Stay();
        $stay2Patient4->setEntranceDate(new DateTime('-10 days'));
        $stay2Patient4->setDischargeDate(new DateTime('-5 days'));
        $stay2Patient4->setreason("Raison du séjour");
        $stay2Patient4->setPatient($patient4);
        $stay2Patient4->setService($service2);
        $stay2Patient4->setDoctor($doctor2);
        $manager->persist($stay2Patient4);

        // Création des prescriptions
        $prescription1 = new Prescription();
        $prescription1->setStartAt(new DateTime('-9 days'));
        $prescription1->setEndAt(new DateTime('-8 days'));
        $prescription1->setDoctor($doctor2);        
        $prescription1->setPatient($patient1);
        $medication = new Medication();
        $medication->setName("Doliprane");
        $medication->setDosage("3 x 1000mg");
        $prescription1->addMedication($medication);
        $manager->persist($medication);
        $manager->persist($prescription1);

        $prescription2 = new Prescription();
        $prescription2->setStartAt(new DateTime('-9 days'));
        $prescription2->setEndAt(new DateTime('-8 days'));
        $prescription2->setDoctor($doctor2);
        $prescription2->setPatient($patient2); 
        $medication2 = new Medication();
        $medication2->setName("Doliprane");
        $medication2->setDosage("3 x 1000mg");       
        $prescription2->addMedication($medication2);
        $manager->persist($medication2);
        $manager->persist($prescription2);

        $prescription3 = new Prescription();
        $prescription3->setStartAt(new DateTime('-9 days'));
        $prescription3->setEndAt(new DateTime('-8 days'));
        $prescription3->setDoctor($doctor2);
        $prescription3->setPatient($patient3); 
        $medication3 = new Medication();
        $medication3->setName("Doliprane");
        $medication3->setDosage("3 x 1000mg");        
        $prescription3->addMedication($medication3);
        $manager->persist($medication3);
        $manager->persist($prescription3);

        $prescription4 = new Prescription();
        $prescription4->setStartAt(new DateTime('-9 days'));
        $prescription4->setEndAt(new DateTime('-8 days'));
        $prescription4->setDoctor($doctor2);
        $prescription4->setPatient($patient4); 
        $medication4 = new Medication();
        $medication4->setName("Doliprane");
        $medication4->setDosage("3 x 1000mg");        
        $prescription4->addMedication($medication4);
        $manager->persist($medication4);
        $manager->persist($prescription4);

        // Création des avis médicaux
        $comment1 = new Comment();
        $comment1->setTitle("Titre avis médical");
        $comment1->setContent("Texte de l'avis médical");
        $comment1->setCreateAt(new DateTime('-8 days'));
        $comment1->setDoctor($doctor2);
        $comment1->setPatient($patient1);
        $manager->persist($comment1);

        $comment2 = new Comment();
        $comment2->setTitle("Titre avis médical");
        $comment2->setContent("Texte de l'avis médical");
        $comment2->setCreateAt(new DateTime('-8 days'));
        $comment2->setDoctor($doctor2);
        $comment2->setPatient($patient2);
        $manager->persist($comment2);

        $comment3 = new Comment();
        $comment3->setTitle("Titre avis médical");
        $comment3->setContent("Texte de l'avis médical");
        $comment3->setCreateAt(new DateTime('-8 days'));
        $comment3->setDoctor($doctor2);
        $comment3->setPatient($patient3);
        $manager->persist($comment3);

        $comment4 = new Comment();
        $comment4->setTitle("Titre avis médical");
        $comment4->setContent("Texte de l'avis médical");
        $comment4->setCreateAt(new DateTime('-8 days'));
        $comment4->setDoctor($doctor2);
        $comment4->setPatient($patient4);
        $manager->persist($comment4);

        $manager->flush();
    }
}
