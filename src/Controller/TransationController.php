<?php

namespace App\Controller;


use App\Form\EnvoieType;
use App\Entity\Transaction;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserCompteActuelRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\CompteRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\TarifsRepository;

class TransationController extends AbstractFOSRestController
{

    /**
     * @Route("/transation/envoie", name="transation_envoie")
     */
    public function send(Request $request,ObjectManager $manager, UserInterface $Userconnecte, ValidatorInterface $validator,UserCompteActuelRepository $repoUserComp,CompteRepository $repoCompt,EntrepriseRepository $repoEn,TarifsRepository $repoTarif)
     {   //$userComp=$repoEn->find(3);
    //     var_dump( $userComp);die();
        $envoie=new Transaction();
        $form = $this->createForm(EnvoieType::class,$envoie);
        $data = json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();
        }
        $form->submit($data);
        if(!$form->isSubmitted() || !$form->isValid()){
            return $this->handleView($this->view($validator->validate($form)));
        }
       
        $tarifs=$repoTarif->findAll();
        for($i=0;$i<count($tarifs);$i++){
            $borneeInf=$tarifs[$i]->getBorneInferieure();
            $borneSup=$tarifs[$i]->getBorneSuperieure();
            $montant= $envoie->getMontant(); 
            if($borneeInf<=$montant && $montant<=$borneSup){
                if(2000001<=$montant && $montant<=3000000){
                     $frais=$montant*0.02; break;
                }else{
                    $frais=$tarifs[$i]->getValeur(); break;
                }
            }
        }
        
        $commissionEmetteur=$frais*0.2;
        $commissionSAT=$frais*0.4;
        $taxesEtat=$frais*0.3;

        //$userComp=$repoUserComp->findOneBy(['utilisateur'=>$Userconnecte]);
        //$userCompActuel=$userComp[count($userComp)-1]->getCompte();
        $userComp=$repoUserComp->findAll();
        var_dump($userComp);die();
        
        $envoie->setDateEnvoi(new \DateTime())
               ->setCode("1111111")
               ->setFrais($frais)
               ->setCommissionEmetteur($commissionEmetteur)
               ->setCommissionWari( $commissionSAT)
               ->setTaxesEtat($taxesEtat)
               ->setUserComptePartenaireEmetteur($userCompActuel)
               ->setStatus('Envoyer');
        $manager->persist($envoie);

        $compteSAT=$repoCompt->findOneBy(['numeroCompte'=>'1910 1409 0043']);
        $compteSAT->setSolde($compteSAT->getSolde()+ $commissionSAT);
        $manager->persist($compteSAT);

        $compteEtat=$repoCompt->findOneBy(['numeroCompte'=>'0221 0445 0443']);
        $compteEtat->setSolde($compteEtat->getSolde()+$taxesEtat);
        $manager->persist($compteEtat);

        $userComp->setSolde($userComp->getSolde()+$commissionEmetteur);
        $manager->persist($userComp);
        $manager->flush();
        $afficher = [
           STATUS => 201,
           MESSAGE => 'Transfert éffectué !'
        ];
        return $this->handleView($this->view($afficher,Response::HTTP_CREATED));
    }
}
