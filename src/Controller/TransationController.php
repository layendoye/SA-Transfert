<?php

namespace App\Controller;


use App\Entity\Compte;
use App\Form\EnvoieType;
use App\Entity\Entreprise;
use App\Entity\Transaction;
use App\Entity\Utilisateur;
use App\Form\TransactionType;
use App\Entity\UserCompteActuel;
use App\Repository\CompteRepository;
use App\Repository\TarifsRepository;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\EntrepriseRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserCompteActuelRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
class TransationController extends AbstractFOSRestController
{
    private $message;
    private $status;
    private $saTransfert;
    private $groups;
    public function __construct()
    {
        $this->message="message";
        $this->status="status";
        $this->saTransfert="SA Transfert";
        $this->groups='groups';
    }

    /**
     * @Route("/transation/envoie", name="transation_envoie")
     * @IsGranted({"ROLE_utilisateur","ROLE_admin","ROLE_admin-Principal"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function envois(Request $request,ObjectManager $manager,ValidatorInterface $validator,TarifsRepository $repoTarif,UserCompteActuelRepository $repoUserComp,CompteRepository $repoCompt,UserInterface $userConnecte)
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
        if(!$userComp){
            throw new HttpException(403,'Vous n\'etes rattachéà aucun compte !');
        }
        elseif($userComp->getCompte()->getSolde()<$montant){
            throw new HttpException(403,'Le solde de votre compte ne vous permet pas de traiter cette transaction !');
        }
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
    /**
    * @Route("/transation/retrait", name="transation_retrait")
    * @IsGranted({"ROLE_utilisateur","ROLE_admin","ROLE_admin-Principal"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
    */
    public function retrait(Request $request,TransactionRepository $repoTrans, ObjectManager $manager,ValidatorInterface $validator,UserCompteActuelRepository $repoUserComp,UserInterface $userConnecte)
    {
        $data = json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();
        }
        $code=$data['code'];
        $retrait=$repoTrans->findOneBy(['code'=>$code]);
        if(!$retrait){
            throw new HttpException(404,'Ce code n\'existe pas !');
        }
        elseif($retrait->getStatus()!='Envoyer'){
            throw new HttpException(403,'Montant déja retiré !');
        }

        $form = $this->createForm(TransactionType::class,$retrait);
        $form->submit($data);

        if(!$form->isSubmitted() || !$form->isValid()){
            return $this->handleView($this->view($validator->validate($form)));
        }
        $montant = $retrait->getMontant();
        $commissionRecep=$retrait->getCommissionEmetteur()/2;//car l'emetteur avait 20% et le recepteur doit en avoir 10 
        
        $userComp=$repoUserComp->findUserComptActu($userConnecte);
        if(!$userComp){
            throw new HttpException(403,'Vous n\'etes rattachéà aucun compte !');
        }
        $retrait->setDateReception(new \DateTime())
                ->setCommissionRecepteur($commissionRecep)
                ->setUserComptePartenaireRecepteur($userComp)
                ->setStatus('Retirer');
        $manager->persist($retrait);
        
        $userComp->getCompte()->setSolde($userComp->getCompte()->getSolde()+$commissionRecep+$montant);
        $manager->persist($userComp);
        
        $manager->flush();
        $afficher = [
           $this->message => 201,
           $this->status => 'Retrait éffectué !'
        ];
        return $this->handleView($this->view($afficher,Response::HTTP_CREATED));
    }

    /**
    * @Route("/transation/user/{action}/{id}", name="transation_user")
    * @IsGranted({"ROLE_utilisateur","ROLE_admin","ROLE_admin-Principal"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
    */
    public function transactionUser($action,TransactionRepository $repoTrans,SerializerInterface $serializer,Utilisateur $user){
        $envois='envois';
        $retraits='retraits';
        if(!$user){
            throw new HttpException(404,'Cet utilisateur n\'existe pas !');
        }
        elseif($action!= $envois && $action!=$retraits){
            throw new HttpException(404,'Resource non trouvée !!');
        }
        $transactionsUser=[];
        $transactions=$repoTrans->findAll();
        for($i=0;$i<count($transactions);$i++){
            $userComptEmetteur=$transactions[$i]->getUserComptePartenaireEmetteur();
            $userComptRecpt=$transactions[$i]->getUserComptePartenaireRecepteur();
            if($userComptEmetteur && $userComptEmetteur->getUtilisateur()==$user && $action== $envois|| $userComptRecpt && $userComptRecpt->getUtilisateur()==$user && $action==$retraits){
                $transactionsUser[]=$transactions[$i];
            }
        }
        if($action== $envois){
             $data = $serializer->serialize($transactionsUser,'json',[ $this->groups => ['list-envois']]);//chercher une alternative pour les groupes avec forest
        }
        else{
            $data = $serializer->serialize($transactionsUser,'json',[ $this->groups => ['list-retraits']]);//chercher une alternative pour les groupes avec forest
        }
        return new Response($data,200);
    }
    /**
    * @Route("/transation/partenaire/{action}/{id}", name="transation_partenaire")
    * @IsGranted({"ROLE_utilisateur","ROLE_admin","ROLE_admin-Principal"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
    */
    public function transactionPartenaire($action,TransactionRepository $repoTrans,SerializerInterface $serializer,Entreprise $entreprise){
        $envois='envois';
        $retraits='retraits';
        if(!$entreprise){
            throw new HttpException(404,'Cette entreprise n\'existe pas !');
        }
        elseif($action!= $envois && $action!=$retraits){
            throw new HttpException(404,'Resource non trouvée !!!!');
        }
        $transactionsPart=[];
        $transactions=$repoTrans->findAll();
        for($i=0;$i<count($transactions);$i++){
            $userComptEmetteur=$transactions[$i]->getUserComptePartenaireEmetteur();
            $userComptRecpt=$transactions[$i]->getUserComptePartenaireRecepteur();
            if($userComptEmetteur && $userComptEmetteur->getUtilisateur()->getEntreprise()==$entreprise && $action== $envois|| $userComptRecpt && $userComptRecpt->getUtilisateur()->getEntreprise()==$entreprise && $action==$retraits){
                $transactionsPart[]=$transactions[$i];
            }
        }
        if($action== $envois){
             $data = $serializer->serialize($transactionsPart,'json',[ $this->groups => ['list-envois']]);//chercher une alternative pour les groupes avec forest
        }
        else{
            $data = $serializer->serialize($transactionsPart,'json',[ $this->groups => ['list-retraits']]);//chercher une alternative pour les groupes avec forest
        }
        return new Response($data,200);
    }    
}
