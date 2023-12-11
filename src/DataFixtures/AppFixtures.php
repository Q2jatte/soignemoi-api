<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;
    
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un user "patient"
        $patient = new User();
        $patient->setEmail("patient@test.com");
        $patient->setRoles(["ROLE_USER"]);
        $patient->setPassword($this->userPasswordHasher->hashPassword($patient, "password"));
        $manager->persist($patient);

        // Création d'un user "staff"
        $staff = new User();
        $staff->setEmail("staff@test.com");
        $staff->setRoles(["ROLE_STAFF"]);
        $staff->setPassword($this->userPasswordHasher->hashPassword($staff, "password"));
        $manager->persist($staff);

        // Création d'un user "doctor"
        $doctor = new User();
        $doctor->setEmail("doctor@test.com");
        $doctor->setRoles(["ROLE_DOCTOR"]);
        $doctor->setPassword($this->userPasswordHasher->hashPassword($doctor, "password"));
        $manager->persist($doctor);
        
        // Création d'un user admin
        $userAdmin = new User();
        $userAdmin->setEmail("admin@test.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $manager->persist($userAdmin);

        $manager->flush();
    }
}
