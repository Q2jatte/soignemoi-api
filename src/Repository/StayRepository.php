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

    // Function to replace all others
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

    // All stays of a patient
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

    // Current stay of a patient
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

    // Previous stays of a patient
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

    // Returns current and future stays
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

    // Returns patients of the day for a doctor
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

    // Returns patients of the day for all doctors
    public function findPatientForAllDoctors(): array
    {
        $today = new \DateTime();

        return $this->createQueryBuilder('p')                   
            ->andWhere('(:today > p.entranceDate AND :today <= p.dischargeDate)')            
            ->setParameter('today', $today)                             
            ->getQuery()
            ->getResult();
    }

    // Stays whose entrance is today
    public function findEntries(): array
    {
        $today = new \DateTime();

        return $this->createQueryBuilder('p')           
            ->andWhere('p.entranceDate BETWEEN :dateMin AND :dateMax')
            ->setParameters(
                [
                    'dateMin' => $today->format('Y-m-d 00:00:00'),
                    'dateMax' => $today->format('Y-m-d 23:59:59'),
                ]
            )             
            ->getQuery()
            ->getResult();
    }

    // Stays whose exit is today
    public function findExits(): array
    {
        $today = new \DateTime();

        return $this->createQueryBuilder('p')           
            ->andWhere('p.dischargeDate BETWEEN :dateMin AND :dateMax')
            ->setParameters(
                [
                    'dateMin' => $today->format('Y-m-d 00:00:00'),
                    'dateMax' => $today->format('Y-m-d 23:59:59'),
                ]
            )             
            ->getQuery()
            ->getResult();
    }

    // Count current stays
    public function findAllCurrentStays()
    {
        $today = new \DateTime();

        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('(:today >= p.entranceDate AND :today <= p.dischargeDate)')
            ->setParameter('today', $today)           
            ->getQuery()
            ->getSingleScalarResult() // Utilisation de getSingleScalarResult pour obtenir un résultat unique
        ;
    }
}
