<?php

namespace App\DataFixtures;

use App\Entity\Compte;
use App\Entity\Profil;
use App\Entity\Entreprise;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $motDePass='$2y$13$ABDZmhTxOlcf4HHbTC3BJeNFZuL/ad/uMTdZgCJZLYYQcSQP0LTKG';
        $actif='Actif';
        $profilSup=new Profil();
        $profilSup->setLibelle('Super-admin');
        $manager->persist($profilSup);
        
        $profilCaiss=new Profil();
        $profilCaiss->setLibelle('Caissier');
        $manager->persist($profilCaiss);
        
        $profilAdP=new Profil();
        $profilAdP->setLibelle('admin-Principal');
        $manager->persist($profilAdP);
        
        $profilAdm=new Profil();
        $profilAdm->setLibelle('admin');
        $manager->persist($profilAdm);
        
        $profilUtil=new Profil();
        $profilUtil->setLibelle('utilisateur');
        $manager->persist($profilUtil);

        $saTransfert=new Entreprise();
        $saTransfert->setRaisonSociale('SA Transfert')
                    ->setNinea(strval(rand(150000000,979999999)))
                    ->setAdresse('Mermoz')
                    ->setStatus($actif);
        $compte=new Compte();
        $compte->setNumeroCompte(date('y').date('m').' '.date('d').date('H').' '.date('i').date('s'))
                   ->setEntreprise($saTransfert);
        $manager->persist($saTransfert);
        $manager->persist($compte);
        
        $SupUser=new Utilisateur();
        $SupUser->setUsername('Abdou')
             ->setRoles(['ROLE_Super-admin'])
             ->setPassword($motDePass)
             ->setConfirmPassword($motDePass)
             ->setEntreprise($saTransfert)
             ->setNom('Abdoulaye Ndoye')
             ->setEmail('layendoyesn@gmail.com')
             ->setTelephone('77 105 01 06')
             ->setNci(strval(rand(150000000,979999999)))
             ->setStatus($actif);
        $manager->persist($SupUser);
        $manager->flush();
    }
}
