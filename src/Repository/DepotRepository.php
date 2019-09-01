<?php

namespace App\Repository;

use App\Entity\Depot;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\Compte;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Depot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Depot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Depot[]    findAll()
 * @method Depot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepotRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Depot::class);
    }

    /**
     * @return Depot[] Returns an array of Depot objects
     */
    public function findMesDepots(Utilisateur $caissier,Compte $compte){
        return $this->createQueryBuilder('d')
            ->andWhere('d.caissier = :val')
            ->andWhere('d.compte = :val2')
            ->setParameter('val', $caissier)
            ->setParameter('val2', $compte)
            ->getQuery()
            ->getResult()
        ;
    }
    

    // /**
    //  * @return Depot[] Returns an array of Depot objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Depot
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
