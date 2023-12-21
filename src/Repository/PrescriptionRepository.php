<?php

namespace App\Repository;

use App\Entity\Prescription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prescription>
 *
 * @method Prescription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prescription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prescription[]    findAll()
 * @method Prescription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrescriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prescription::class);
    }
/*
    public function findPrescriptionsByPatient($patient): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.patient = :val')
            ->setParameter('val', $patient)
            ->orderBy('p.startAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
*/
    
    public function findPrescriptionsByPatient($patient)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('App\Entity\Medication', 'm', 'WITH', 'm.prescription = p')
            ->select('p', 'm') // Sélectionner p et m pour les inclure dans le résultat
            ->andWhere('p.patient = :val')
            ->setParameter('val', $patient)
            ->orderBy('p.startAt', 'DESC')            
            ->getQuery()
            ->getResult();           
    }

//    /**
//     * @return Prescription[] Returns an array of Prescription objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Prescription
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
