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
    private $envois;
    private $retraits;
    private $dateDebut;
    private $dateFin;
    private $listRetraits;
    private $listEnvois;
    public function __construct()
    {
        $this->message="message";
        $this->status="status";
        $this->saTransfert="SA Transfert";
        $this->groups='groups';
        $this->envois='envois';
        $this->retraits='retraits';
        $this->dateDebut='dateDebut';
        $this->dateFin='dateFin';
        $this->listRetraits='list-retraits';
        $this->listEnvois='list-envois';
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

        $userComp->getCompte()->setSolde($userComp->getCompte()->getSolde()+$commissionEmetteur-$montant-$frais);//ancien solde - montant - 80% frais
        $manager->persist($userComp);
        $manager->flush();
        $afficher = $this->recuDeTransaction('envoi',$envoie);
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
            throw new HttpException(403,'Vous n\'etes rattaché à aucun compte !');
        }
        $retrait->setDateReception(new \DateTime())
                ->setCommissionRecepteur($commissionRecep)
                ->setUserComptePartenaireRecepteur($userComp)
                ->setStatus('Retirer');
        $manager->persist($retrait);
        
        $userComp->getCompte()->setSolde($userComp->getCompte()->getSolde()+$commissionRecep+$montant);
        $manager->persist($userComp);
        
        $manager->flush();
        $afficher =  $this->recuDeTransaction('retrait',$retrait);
        return $this->handleView($this->view($afficher,Response::HTTP_CREATED));
    }
    
    /**
    * @Route("/transation/user/{action}/{id}", name="transation_user")
    * @IsGranted({"ROLE_utilisateur","ROLE_admin","ROLE_admin-Principal"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
    */
    public function transactionUser(Request $request,$action,TransactionRepository $repoTrans,SerializerInterface $serializer,Utilisateur $user,UserInterface $userConnecte){

        $data = json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();
        }

        if(!isset($data[$this->dateDebut],$data[$this->dateFin])){
            throw new HttpException(404,'Il y a une erreur sur les données transmises !');
        }
        $debut=$data[$this->dateDebut];
        $fin=$data[$this->dateFin];
        if(!$user){
            throw new HttpException(404,'Cet utilisateur n\'existe pas !');
        }
        elseif($action!= $this->envois && $action!=$this->retraits){
            throw new HttpException(404,'Resource non trouvée !!');
        }
        elseif($user->getEntreprise()!=$userConnecte->getEntreprise()){
            throw new HttpException(403,'Cet utilisateur n\'est pas membre de votre entreprise !!');
        }
        elseif($userConnecte->getRoles()[0]=='ROLE_utilisateur' &&  $user!=$userConnecte){
            throw new HttpException(403,'Vous n\'avez pas acces à ce contenu !!');
        }
        $transactionsUser=$this->transationDate($repoTrans,$debut,$fin,$action,$user);
        if($transactionsUser==[]){
            return $this->handleView($this->view(['Résultat'=>'Aucune transaction trouvée !!!!'],404));
        }
        if($action == $this->envois){
             $values = $serializer->serialize($transactionsUser,'json',[ $this->groups => [$this->listEnvois]]);//chercher une alternative pour les groupes avec forest
        }
        else{
            $values = $serializer->serialize($transactionsUser,'json',[ $this->groups => [$this->listRetraits]]);//chercher une alternative pour les groupes avec forest
        }
        return new Response($values,200);
    }
    
    /**
    * @Route("/transation/partenaire/{action}/{id}", name="transation_partenaire")
    * @IsGranted({"ROLE_utilisateur","ROLE_admin","ROLE_admin-Principal"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
    */
    public function transactionPartenaire(Request $request,$action,TransactionRepository $repoTrans,SerializerInterface $serializer,Entreprise $entreprise,UserInterface $userConnecte){
        $data = json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();
        }

        if(!isset($data[$this->dateDebut],$data[$this->dateFin])){
            throw new HttpException(404,'Il y a une erreur sur les données transmises !');
        }
        $debut=$data[$this->dateDebut];
        $fin=$data[$this->dateFin];
        if(!$entreprise){
            throw new HttpException(404,'Cette entreprise n\'existe pas !');
        }
        elseif($action!= $this->envois && $action!=$this->retraits){
            throw new HttpException(404,'Resource non trouvée !!!!');
        }
        elseif($userConnecte->getRoles()[0]!='ROLE_Super-admin' &&  $entreprise!=$userConnecte->getEntreprise()){
            throw new HttpException(403,'Vous n\'avez pas acces à ce contenu !!');
        }
        $transactionsPart=$this->transationDate($repoTrans,$debut,$fin,$action,null,$entreprise);
        if($transactionsPart==[]){
            return $this->handleView($this->view(['Résultat'=>'Aucune transaction trouvée !!!!'],404));
        }
        if($action== $this->envois){
             $data = $serializer->serialize($transactionsPart,'json',[ $this->groups => [$this->listEnvois]]);//chercher une alternative pour les groupes avec forest
        }
        else{
            $data = $serializer->serialize($transactionsPart,'json',[ $this->groups => [$this->listRetraits]]);//chercher une alternative pour les groupes avec forest
        }
        return new Response($data,200);
    } 
    /**
     * @Route("/info/transaction", name="infoTransaction", methods={"POST"})
     * @IsGranted({"ROLE_utilisateur","ROLE_admin-Principal","ROLE_admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function infoTransaction(Request $request,SerializerInterface $serializer,TransactionRepository $repo){
        $data = json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();
        }
        $code=$data["code"];
        $transation=$repo->findOneBy(["code"=>$code]);
        
        $data = $serializer->serialize($transation,'json',[ $this->groups => [$this->listRetraits,"list-envois"]]);//chercher une alternative pour les groupes avec forest
        return new Response($data,200);
    }
    public function transationDate(TransactionRepository $repoTrans,$debut,$fin,$action, Utilisateur $user=null,Entreprise $entreprise=null){
        $transactionsAll=[];
        $transactions=$repoTrans->findAll();
        $debut=new \DateTime($debut);
        $fin=new \DateTime($fin);
        if($debut>$fin){
            throw new HttpException(403,'Impossible car la date de début est superieure à la date de fin !');
        }
        elseif($fin>new \DateTime()){
            throw new HttpException(403,'Impossible car la date de fin est superieure à la date actuelle !');
        }
        for($i=0;$i<count($transactions);$i++){
            $dateEnvois=new \DateTime($transactions[$i]->getDateEnvoi()->format('Y-m-d'));//$transactions[$i]->getDateEnvoi() seulement renvois un objet de type date avec les minutes et les secondes donc 2019-08-11 < 2019-08-11 08h22 et $transactions[$i]->getDateEnvoi()->format('Y-m-d') renvois une chaine de caracteres pour le remettre en objet date j ai utilisé le new \DateTime()
            $dateRetrait=new \DateTime($transactions[$i]->getDateReception()->format('Y-m-d'));
            $userComptEmetteur=$transactions[$i]->getUserComptePartenaireEmetteur();
            $userComptRecpt=$transactions[$i]->getUserComptePartenaireRecepteur();
            $cas1 = ($action == $this->envois   && $userComptEmetteur && $debut <= $dateEnvois  && $dateEnvois  <= $fin);//si la transaction est un envois et que le $userComptEmetteur existe et que la date est entre le debut et la fin ça retourne true
            $cas2 = ($action == $this->retraits && $userComptRecpt    && $debut <= $dateRetrait && $dateRetrait <= $fin);
            
            if($user       && $cas1 && $userComptEmetteur->getUtilisateur()                  == $user       || 
               $user       && $cas2 && $userComptRecpt->getUtilisateur()                     == $user       ||
               $entreprise && $cas1 && $userComptEmetteur->getUtilisateur()->getEntreprise() == $entreprise ||
               $entreprise && $cas2 && $userComptRecpt->getUtilisateur()->getEntreprise()    == $entreprise   )
            {
                $transactionsAll[]=$transactions[$i];
            } 
            
        }
        return $transactionsAll;
    }
    public function formatMil($valeur){ //permet d afficher le separateur de millier
        return strrev(wordwrap(strrev($valeur), 3, ' ', true));
    }
    public function recuDeTransaction($type,Transaction $transaction){
        $senegal='Sénégal';
        $tel='Téléphone';
        if($type=='envoi'){
            $libelle="Reçu d'envoi";
            $guichetier=$transaction->getUserComptePartenaireEmetteur()->getUtilisateur();
            $entreprise= $guichetier->getEntreprise();
            $date=$transaction->getDateEnvoi();
            $envoyeur=[
                'Nom'=>$transaction->getNomClientEmetteur(),
                'Pays'=>$senegal,
                $tel=>$transaction->getTelephoneEmetteur(),
                'NCI'=>$transaction->getNciEmetteur()
            ];
            $beneficiaire=[
                'Nom'=>$transaction->getNomClientRecepteur(),
                 'Pays'=>$senegal,
                $tel=>$transaction->getTelephoneRecepteur(),
            ];
            $trans=[
                'CodeTransaction'=>$transaction->getCode(),
                'MontantEnvoyé'=>$this->formatMil($transaction->getMontant()).' CFA',
                'CommissionsTTC'=>$this->formatMil($transaction->getFrais()).' CFA',
                'Total'=>$this->formatMil($transaction->getMontant()+$transaction->getFrais()).' CFA'
            ];
        }
        else{
            $libelle="Reçu de retrait";
            $guichetier=$transaction->getUserComptePartenaireRecepteur()->getUtilisateur();
            $entreprise= $guichetier->getEntreprise();
            $date=$transaction->getDateReception();
            $envoyeur=[
                'Nom'=>$transaction->getNomClientEmetteur(),
                'Pays'=>$senegal,
                $tel=>$transaction->getTelephoneEmetteur(),
            ];
            $beneficiaire=[
                'Nom'=>$transaction->getNomClientRecepteur(),
                'Pays'=>$senegal,
                $tel=>$transaction->getTelephoneRecepteur(),
                'NCI'=>$transaction->getNciRecepteur()
            ];
            $trans=[
                'CodeTransaction'=>$transaction->getCode(),
                'MontantRetiré'=> $this->formatMil($transaction->getMontant()).' CFA',
            ];
        }
        return [
            'Type'=>$libelle,
            'Entreprise'=>[
                'RaisonSociale'=>$entreprise->getRaisonSociale(),
                'Adresse'=>$entreprise->getAdresse(),
                $tel=>$entreprise->getTelephoneEntreprise(),
                'Guichetier'=>$guichetier->getNom(),
                'Date'=> $date->format('d-m-Y H:i')
            ],
            'Envoyeur'=> $envoyeur,
            'Bénéficiaire'=> $beneficiaire,
            'Transaction'=>$trans
        ];
    }

}
