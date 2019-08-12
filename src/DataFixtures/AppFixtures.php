<?php

namespace App\DataFixtures;

use App\Entity\Compte;
use App\Entity\Profil;
use App\Entity\Entreprise;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;
    }
    public function load(ObjectManager $manager)
    {
        
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
                    ->setTelephoneEntreprise('0000011')
                    ->setEmailEntreprise('sat@gmail.com')
                    ->setStatus($actif);
        $compte=new Compte();
        $compte->setNumeroCompte('1910 1409 0043')
                   ->setEntreprise($saTransfert);
        $manager->persist($saTransfert);
        $manager->persist($compte);

        $etat=new Entreprise();
        $etat->setRaisonSociale('Etat du Sénégal')
                    ->setNinea('000 000 000')
                    ->setAdresse('Tresor public')
                    ->setTelephoneEntreprise('338541296')
                    ->setEmailEntreprise('tresor-public@gouv.sn')
                    ->setStatus($actif);
        $compte=new Compte();
        $compte->setNumeroCompte('0221 0445 0443')
                   ->setEntreprise($etat);
        $manager->persist($etat);
        $manager->persist($compte);
        
        $SupUser=new Utilisateur();
        $motDePass=$this->encoder->encodePassword($SupUser, 'azerty');
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
