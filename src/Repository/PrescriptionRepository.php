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

    public function findPrescriptionsByPatient($patient): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.medications', 'm')
            ->andWhere('p.patient = :val')
            ->setParameter('val', $patient)
            ->orderBy('p.startAt', 'DESC')           
            ->getQuery()
            ->getResult();
    }
}
