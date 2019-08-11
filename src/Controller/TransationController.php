<?php

namespace App\Controller;


use App\Entity\Compte;
use App\Form\EnvoieType;
use App\Entity\Entreprise;
use App\Entity\Transaction;
use App\Entity\Utilisateur;
use App\Entity\UserCompteActuel;
use App\Repository\CompteRepository;
use App\Repository\TarifsRepository;
use App\Repository\EntrepriseRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserCompteActuelRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TransationController extends AbstractFOSRestController
{
    private $message;
    private $status;
    private $saTransfert;
    public function __construct()
    {
        $this->message="message";
        $this->status="status";
        $this->saTransfert="SA Transfert";
    }

    /**
     * @Route("/transation/envoie", name="transation_envoie")
     */
    public function send(Request $request,ObjectManager $manager,ValidatorInterface $validator,TarifsRepository $repoTarif,UserCompteActuelRepository $repoUserComp,CompteRepository $repoCompt,UserInterface $userConnecte)
    {   
        
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
       
        $montant = $envoie->getMontant(); 
        $tarifs  = $repoTarif->findAll();
        for($i=0;$i<count($tarifs);$i++){
            $borneeInf=$tarifs[$i]->getBorneInferieure();
            $borneSup=$tarifs[$i]->getBorneSuperieure();
            
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

        
        $userComp=$repoUserComp->findUserComptActu($userConnecte);
        
        $code=date('s').date('i').' '.date('H').date('d').' '.date('m').date('Y');
        $envoie->setDateEnvoi(new \DateTime())
               ->setCode($code)
               ->setFrais($frais)
               ->setCommissionEmetteur($commissionEmetteur)
               ->setCommissionWari( $commissionSAT)
               ->setTaxesEtat($taxesEtat)
               ->setUserComptePartenaireEmetteur($userComp)
               ->setStatus('Envoyer');
        $manager->persist($envoie);

        $compteSAT=$repoCompt->findOneBy(['numeroCompte'=>'1910 1409 0043']);
        $compteSAT->setSolde($compteSAT->getSolde()+ $commissionSAT);
        $manager->persist($compteSAT);

        $compteEtat=$repoCompt->findOneBy(['numeroCompte'=>'0221 0445 0443']);
        $compteEtat->setSolde($compteEtat->getSolde()+$taxesEtat);
        $manager->persist($compteEtat);

        $userComp->getCompte()->setSolde($userComp->getCompte()->getSolde()+$commissionEmetteur-$montant);
        $manager->persist($userComp);
        $manager->flush();
        $afficher = [
           $this->message => 201,
           $this->status => 'Transfert éffectué !'
        ];
        return $this->handleView($this->view($afficher,Response::HTTP_CREATED));
    }
}
