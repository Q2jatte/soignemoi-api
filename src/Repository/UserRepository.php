<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    // retourne les patients en fonction d'une partie du nom
    
    public function findPatient($partial): array
    {   
        
        return $this->createQueryBuilder('u')
            ->andWhere('u.firstName LIKE :partial OR u.lastName LIKE :partial') 
            ->andWhere('u.patient IS NOT NULL')          
            ->setParameter('partial', '%' . $partial . '%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
            
/*
            $rsm = new ResultSetMappingBuilder($this->getEntityManager());
            $rsm->addRootEntityFromClassMetadata('App\Entity\User', 'u');

            $query = $this->getEntityManager()->createNativeQuery(
                'SELECT * FROM user WHERE (first_name LIKE :partial OR last_name LIKE :partial) AND patient_id IS NOT NULL LIMIT 10', $rsm);

            $query->setParameter('partial', '%' . $partial . '%');

            return $query->getResult();*/
    }

        
}
