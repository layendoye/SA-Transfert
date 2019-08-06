<?php

namespace App\Repository;

use App\Entity\UserCompteActuel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserCompteActuel|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCompteActuel|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCompteActuel[]    findAll()
 * @method UserCompteActuel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCompteActuelRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserCompteActuel::class);
    }

    // /**
    //  * @return UserCompteActuel[] Returns an array of UserCompteActuel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserCompteActuel
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
