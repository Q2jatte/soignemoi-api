<?php

namespace App\Repository;

use App\Entity\Stay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stay>
 *
 * @method Stay|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stay|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stay[]    findAll()
 * @method Stay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stay::class);
    }

    // Une fonction pour les remplcer toutes
    public function findStaysByPatientAndStatus($patient, $status): array
    {        
        $today = new \DateTime();

        $queryBuilder = $this->createQueryBuilder('p')
            ->andWhere('p.patient = :patient')
            ->setParameter('patient', $patient);

        if ($status === 'all') {
            return $queryBuilder
                ->orderBy('p.dischargeDate', 'DESC')
                ->getQuery()
                ->getResult();
        } else {
            return $queryBuilder
                ->andWhere(
                    '(CASE 
                        WHEN :today > p.entranceDate AND :today <= p.dischargeDate THEN \'current\'
                        WHEN :today > p.dischargeDate THEN \'old\'
                        WHEN :today < p.entranceDate THEN \'future\'
                        ELSE \'all\'                 
                    END) = :status'
                )
                ->setParameter('today', $today)
                ->setParameter('status', $status)
                ->orderBy('p.dischargeDate', 'DESC')
                ->getQuery()
                ->getResult();
        }             
    }

    // Tout les séjours d'un patient
    public function findStaysByPatient($patient): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('p.dischargeDate', 'DESC')            
            ->getQuery()
            ->getResult()
        ;
    }

    // Séjour en cours d'un patient
    public function findCurrentStay($patient): array
    {
        $today = new \DateTime();

        return $this->createQueryBuilder('p')
            ->andWhere('p.patient = :patient')
            ->andWhere('(:today > p.entranceDate AND :today <= p.dischargeDate)')
            ->setParameter('patient', $patient)
            ->setParameter('today', $today)
            ->orderBy('p.dischargeDate', 'DESC')            
            ->getQuery()
            ->getResult()
        ;
    }

    // Séjours précédent d'un patient
    public function findOldStays($patient): array
    {
        $today = new \DateTime();

        return $this->createQueryBuilder('p')
            ->andWhere('p.patient = :patient')
            ->andWhere('(:today > p.dischargeDate)')
            ->setParameter('patient', $patient)
            ->setParameter('today', $today)
            ->orderBy('p.dischargeDate', 'DESC')            
            ->getQuery()
            ->getResult()
        ;
    }

    // retourne les séjours en cours et à venir
    public function findStaysByDoctor($doctor): array
    {
        $today = new \DateTime();

        return $this->createQueryBuilder('p')
            ->andWhere('p.doctor = :doctor')
            ->andWhere('(:today BETWEEN p.entranceDate AND p.dischargeDate) OR (:today < p.entranceDate)')
            ->setParameter('doctor', $doctor)
            ->setParameter('today', $today)
            ->orderBy('p.dischargeDate', 'DESC')            
            ->getQuery()
            ->getResult()
        ;
    }

    // retourne les patients du jour pour un docteur
    public function findPatientByDoctor($doctor): array
    {
        $today = new \DateTime();

        return $this->createQueryBuilder('p')
            ->andWhere('p.doctor = :doctor')
            ->andWhere('(:today > p.entranceDate AND :today <= p.dischargeDate)')
            ->setParameter('doctor', $doctor)
            ->setParameter('today', $today)
            ->orderBy('p.dischargeDate', 'DESC')            
            ->getQuery()
            ->getResult();
    }
}
