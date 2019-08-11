<?php

namespace App\Repository;

use App\Entity\UserCompteActuel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\Entreprise;
use App\Entity\Utilisateur;

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
    public function findByEntreprise(Entreprise $entreprise){
        $valeurs=null;
        $tous=$this->findAll();
        for($i=0;$i<count($tous);$i++){
            if($tous[$i]->getCompte()->getEntreprise()==$entreprise){
                $valeurs[]=$tous[$i];
            }
        }
        return $valeurs;
    }
    public function findUserComptActu(Utilisateur $user){
        $tous=$this->findBy(['utilisateur'=>$user]);
        return $tous[count($tous)-1];
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