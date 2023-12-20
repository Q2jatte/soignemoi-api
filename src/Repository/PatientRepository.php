<?php

namespace App\Repository;

use App\Entity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Patient>
 *
 * @method Patient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Patient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Patient[]    findAll()
 * @method Patient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patient::class);
    }  
    
    public function findPatientByName($query)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u') // Assurez-vous que 'user' est le nom de votre relation dans l'entité Patient            
            ->andWhere('u.firstName LIKE :query OR u.lastName LIKE :query') 
            ->andWhere('u.patient IS NOT NULL')  
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }
}
