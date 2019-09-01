<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Compte;
use App\Form\DepotType;
use App\Entity\Entreprise;
use App\Entity\Utilisateur;
use App\Form\EntrepriseType;
use App\Form\UtilisateurType;
use App\Repository\CompteRepository;
use App\Repository\DepotRepository;
use App\Repository\EntrepriseRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\UtilisateurRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\UserCompteActuelRepository;
use App\Entity\UserCompteActuel;
use SebastianBergmann\CodeCoverage\Util;
use Symfony\Component\Security\Core\User\User;

class EntrepriseController extends AbstractFOSRestController
{

    private $actif;
    private $message;
    private $status;
    private $saTransfert;
    private $groups;
    private $contentType;
    private $utilisateurStr;
    private $compteStr;
    private $bloqueStr;
    private $listUserCmpt;
    public function __construct()
    {
        $this->actif="Actif";
        $this->message="message";
        $this->status="status";
        $this->saTransfert="SA Transfert";
        $this->groups='groups';
        $this->contentType='Content-Type';
        $this->utilisateurStr='utilisateur';
        $this->compteStr="compte";
        $this->bloqueStr='Bloqué';
        $this->listUserCmpt='list-userCmpt';
    }
    /**
     * @Route("/entreprises/liste", name="entreprises", methods={"GET"})
     * @Route("/entreprise/{id}", name="entreprise", methods={"GET"})
     * @IsGranted({"ROLE_Super-admin","ROLE_Caissier"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function lister(EntrepriseRepository $repo, SerializerInterface $serializer,Entreprise $entreprise=null,$id=null)
    {
        
        if($id && !$entreprise instanceof Entreprise) {
            throw new HttpException(404,'Ce partenaire n\'existe pas!');
        }
        if(!$entreprise){
            $entreprise=$repo->findPartenaire();
        }
        $data = $serializer->serialize($entreprise,'json',[ $this->groups => ['list-entreprise']]);//chercher une alternative pour les groupes avec forest
        return new Response($data,200,[$this->contentType => 'application/json']);
    }
    /**
     * @Route("/partenaires/add", name="add_entreprise", methods={"POST"})
     * @IsGranted({"ROLE_Super-admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function addPartenaire(Request $request, ObjectManager $manager, ValidatorInterface $validator,UserPasswordEncoderInterface $encoder)
    {
        #####################----------Début traitement formulaire et envoie des données----------#####################
            $raisonSociale='raisonSociale';
            $ninea='ninea';
            $adresse='adresse';
            $telephoneEntreprise='telephoneEntreprise';
            $emailEntreprise='emailEntreprise';
            $entreprise = new Entreprise();
            $form1=$this->createForm(EntrepriseType::class,$entreprise);
            $data=json_decode($request->getContent(),true);
            if(!$data){//s il n'existe pas donc on recupere directement le tableau via la request
                $data=$request->request->all();
            }
            ###########---Début données partenaire---###########
                $dataPartenaire=array(
                    $raisonSociale=>$data[$raisonSociale],
                    $ninea=>$data[$ninea],
                    $adresse=>$data[$adresse],
                    $telephoneEntreprise=>$data[$telephoneEntreprise],
                    $emailEntreprise=>$data[$emailEntreprise]
                );
                $form1->submit($dataPartenaire);
                if(!$form1->isSubmitted() || !$form1->isValid()){
                    return $this->handleView($this->view($validator->validate($form1)));
                }
            ###########----Fin données partenaire----###########

            ###########---Début données utilisateur---###########
                unset($data[$raisonSociale],$data[$ninea],$data[$adresse],$data[$telephoneEntreprise],$data[$emailEntreprise]);# on supprime les données du partenaire
                $user=new Utilisateur();
                $form2=$this->createForm(UtilisateurType::class,$user);
                $form2->submit($data);
                if(!$form2->isSubmitted() || !$form2->isValid()){
                    return $this->handleView($this->view($validator->validate($form2)));
                }
            ###########----Fin données utilisateur----###########
        #####################-----------Fin traitement formulaire et envoie des données-----------#####################
        
        #####################---------------Début gestion entreprise, compte et user--------------#####################
        
            $entreprise->setStatus($this->actif);
            $manager->persist($entreprise); 
            $compte=new Compte();
            $compte->setNumeroCompte(date('y').date('m').' '.date('d').date('H').' '.date('i').date('s'))
                   ->setEntreprise($entreprise);
            $manager->persist($compte);

            $user->setRoles(['ROLE_admin-Principal'])
                ->setEntreprise($entreprise)
                ->setStatus($this->actif);
            $hash=$encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);

        #####################----------------Fin gestion entreprise, compte et user---------------#####################
        #####################------------------------Début gestion des images---------------------#####################
            
            if($requestFile=$request->files->all()){
                $file=$requestFile['image'];
                if($file->guessExtension()!='png' && $file->guessExtension()!='jpeg' ){
                    throw new HttpException(400,'Entrer une image valide !! ');
                }
                
                $fileName=md5(uniqid()).'.'.$file->guessExtension();//on change le nom du fichier
                $user->setImage($fileName);
                $file->move($this->getParameter('image_directory'),$fileName); //definir le image_directory dans service.yaml
            }

        #####################-------------------------Fin gestion des images----------------------#####################
        
        #####################---------------------------Début finalisation------------------------#####################
            $manager->flush();
            $afficher = [
                $this->status => 201,
                $this->message => 'Le partenaire '.$entreprise->getRaisonSociale().' ainsi que son admin principal ont bien été ajouté !! ',
               'compte' =>'Le compte numéro '.$compte->getNumeroCompte().' lui a été assigné'
            ];
            return $this->handleView($this->view($afficher,Response::HTTP_CREATED));
        #####################----------------------------Fin finalisation-------------------------#####################
    }

    /**
    * @Route("/partenaires/update/{id}", name="update_entreprise", methods={"POST"})
    * @IsGranted({"ROLE_Super-admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
    */
    public function updatePartenaire(Entreprise $entreprise,Request $request, ObjectManager $manager, ValidatorInterface $validator){
        #####################----------Début traitement formulaire et envoie des données----------#####################
            if(!$entreprise){
                throw new HttpException(404,'Cette entreprise n\'existe pas !');
            }
            $form=$this->createForm(EntrepriseType::class,$entreprise);
            $data=json_decode($request->getContent(),true);//si json
            if(!$data){
                $data=$request->request->all();//si non json
            }
            $form->submit($data);
            if(!$form->isSubmitted() || !$form->isValid()){
                return $this->handleView($this->view($validator->validate($form)));
            }
        #####################-----------Fin traitement formulaire et envoie des données-----------#####################

        #####################---------------------------Début finalisation------------------------#####################
            $entreprise->setStatus($this->actif); 
            $manager->persist($entreprise); 
            $manager->flush();
            $afficher = [
                $this->status => 200,
                $this->message => 'Le partenaire a été correctement modifié !'
            ];
            return $this->handleView($this->view($afficher,Response::HTTP_OK));
        #####################----------------------------Fin finalisation-------------------------#####################
    }
    /**
    * @Route("/nouveau/depot", methods={"POST"})
    * @IsGranted({"ROLE_Caissier"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
    */
    public function depot (Request $request, ValidatorInterface $validator, UserInterface $Userconnecte,CompteRepository $repo, ObjectManager $manager){
        $depot = new Depot();
        $form = $this->createForm(DepotType::class, $depot);
        $data=json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();//si non json
        }
        if($compte=$repo->findOneBy(['numeroCompte'=>$data[$this->compteStr]])){
            $data[$this->compteStr]=$compte->getId();//on lui donne directement l'id
            if($compte->getEntreprise()->getRaisonSociale()==$this->saTransfert){
                throw new HttpException(403,'On ne peut pas faire de depot dans le compte de SA Transfert !');
            }
        }
        else{
            throw new HttpException(404,'Ce numero de compte n\'existe pas !');
        }
        $form->submit($data);

        if($form->isSubmitted() && $form->isValid())
        {
           $depot->setDate(new \DateTime());
           $depot->setCaissier($Userconnecte);
           $compte=$depot->getCompte();
           $compte->setSolde($compte->getSolde()+$depot->getMontant());
           $manager->persist($compte);
           $manager->persist($depot);
           $manager->flush();
           $afficher = [
                $this->status => 201,
                $this->message => 'Le depot a bien été effectué dans le compte '.$compte->getNumeroCompte()
           ];
           return $this->handleView($this->view($afficher,Response::HTTP_CREATED));

        }
        return $this->handleView($this->view($validator->validate($form)));
    }
    /**
    * @Route("/bloque/entreprises/{id}", name="bloque_entreprise", methods={"GET"})
    * @IsGranted({"ROLE_Super-admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
    */ 
    public function bloqueEntrep(ObjectManager $manager,Entreprise $entreprise=null)
    {
        if(!$entreprise){
            throw new HttpException(404,'Ce partenaire n\'existe pas !!');
        }
        elseif($entreprise->getRaisonSociale()==$this->saTransfert){
            throw new HttpException(403,'Impossible de bloquer SA Transfert !');
        }
        elseif($entreprise->getRaisonSociale()=='Etat du Sénégal'){
            throw new HttpException(403,'Impossible de bloquer l\'etat du Sénégal !');
        }
        elseif($entreprise->getStatus() == $this->actif){
            $entreprise->setStatus($this->bloqueStr);
            $texte= 'Partenaire bloqué';
        }
        else{
            $entreprise->setStatus($this->actif);
            $texte= 'Partenaire débloqué';
        }
        $manager->persist($entreprise);
        $manager->flush();
        $afficher = [ $this->status => 200, $this->message => $texte];
        return $this->handleView($this->view($afficher,Response::HTTP_OK));
    }
    /**
    * @Route("/bloque/user/{id}", name="bloque_user", methods={"GET"})
    * @IsGranted({"ROLE_Super-admin","ROLE_admin-Principal","ROLE_admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
    */ 
    public function bloqueUser(UserInterface $Userconnecte,ObjectManager $manager, Utilisateur $user=null)
    {
        
        if(!$user){
            throw new HttpException(404,'Cet utilisateur n\'existe pas !');
        }
        if($user==$Userconnecte){
            throw new HttpException(403,'Impossible de se bloquer soit même !');
        }
        $entreprise=$user->getEntreprise(); 
        if($Userconnecte->getEntreprise()!=$entreprise){//si un super admin et caissier sont dans la meme entreprises les admin principaux les admins et les users simple aussi
            throw new HttpException(403,'Impossible de bloquer cet utilisateur !');
        }
        elseif($user->getId()==1){
            throw new HttpException(403,'Impossible de bloquer le super-admin principal !');
        }
        if($Userconnecte->getRoles()[0]=='ROLE_admin' && $user->getRoles()[0]=='ROLE_admin-Principal'){
            throw new HttpException(403,'Impossible de bloquer l\' admin principal !');
        }
        
        if($user->getStatus() == $this->actif){
            $user->setStatus($this->bloqueStr);
            $texte=$this->bloqueStr;
        }
        else{
            $user->setStatus($this->actif);
            $texte= 'Débloqué';
        }
        $manager->persist($user);
        $manager->flush();
        $afficher = [ $this->status => 200, $this->message => $texte];
        return $this->handleView($this->view($afficher,Response::HTTP_OK));
    }

    /**
    * @Route("/nouveau/compte/{id}", name="nouveau_compte", methods={"GET"})
    * @IsGranted("ROLE_Super-admin", statusCode=403, message="Vous n'avez pas accès à cette page !")
    */ 
    public function addCompte(ObjectManager $manager, Entreprise $entreprise){//securiser la route
        $compte =new Compte();
        if(!$entreprise){
            throw new HttpException(404,'Ce partenaire n\'existe pas !');
        }
        elseif($entreprise->getRaisonSociale()==$this->saTransfert){
            throw new HttpException(403,'Impossible de créer plusieurs compte pour SA Transfert!');
        }
        $compte->setNumeroCompte(date('y').date('m').' '.date('d').date('H').' '.date('i').date('s'))
                   ->setEntreprise($entreprise);
           
        $manager->persist($compte);
        $manager->flush();
        $afficher = [
            $this->status => 201,
            $this->message => 'Un nouveau compte est créé pour l\'entreprise '.$entreprise->getRaisonSociale(),
            'compte'=> $compte->getNumeroCompte()
        ];
        return $this->handleView($this->view($afficher,Response::HTTP_OK));
    }
    /**
     * @Route("/changer/compte" ,name="change_compte")
     * @IsGranted("ROLE_admin-Principal", statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function changeCompte(Request $request,ObjectManager $manager, UserInterface $Userconnecte,UtilisateurRepository $repoUser,CompteRepository $repoCompte,UserCompteActuelRepository $repoUserComp)
    {   
        $data=json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();//si non json
        }
        if(!isset($data[$this->utilisateurStr],$data[$this->compteStr])){
            throw new HttpException(404,'Remplir un utilisateur et  cunompte existant!');
        }
        elseif(!$user=$repoUser->find($data[$this->utilisateurStr])){
            throw new HttpException(404,'Cet utilisateur n\'existe pas !');
        }
        elseif($user->getRoles()[0]=='ROLE_Super-admin' || $user->getRoles()[0]=='ROLE_Caissier'){
            throw new HttpException(403,'Impossible d\'affecter un compte à et utilisateur !');
        }
        elseif($user->getEntreprise()!=$Userconnecte->getEntreprise()){
            throw new HttpException(403,'Cet utilisateur n\'appartient pas à votre entreprise !');
        }
        if(!$compte=$repoCompte->find($data[$this->compteStr])){
            throw new HttpException(404,'Ce compte n\'existe pas !');
        }
        elseif($compte->getEntreprise()!=$Userconnecte->getEntreprise()){
            throw new HttpException(404,'Ce compte n\'appartient pas à votre entreprise !');
        }
        $idcompActuel=null;
        if($userComp=$repoUserComp->findBy([$this->utilisateurStr=>$user])){
            $idcompActuel=$userComp[count($userComp)-1]->getCompte()->getId();//l id du compte qu il utilise actuellement
        }
        
        if($idcompActuel==$compte->getId()){
            throw new HttpException(403,'Cet utilisateur utilise ce compte actuellement!');
        }
        $userCompte=new UserCompteActuel();

        $userCompte->setCompte($compte)
                   ->setUtilisateur($user)
                   ->setDateAffectation(new \DateTime());
        $manager->persist($userCompte);
        $manager->flush();
        $afficher = [
                $this->status => 201,
                $this->message => 'Le compte de l\'utilisateur a été modifié !!'
           ];
        return $this->handleView($this->view($afficher,Response::HTTP_OK));
    }

    /**
     * @Route("/compte/entreprise/{id}", name="compte_entr", methods={"GET"})
     * @Route("/MesComptes", name="compte_userCon", methods={"GET"})
     * @IsGranted({"ROLE_Super-admin","ROLE_admin-Principal","ROLE_admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function getCompte(UserInterface $userConnecte, SerializerInterface $serializer,Entreprise $entreprise=null,$id=null)
    {
        
        if($id && !$entreprise instanceof Entreprise) {
            throw new HttpException(404,'Ce partenaire n\'existe pas!');
        }
        elseif(!$id){
            $entreprise=$userConnecte->getEntreprise();
            
        }
        
        $data = $serializer->serialize($entreprise->getComptes(),'json',[ $this->groups => ['list-compte']]);//chercher une alternative pour les groupes avec forest
        return new Response($data,200,[$this->contentType => 'application/json']);
    }

    /**
     * @Route("/gestion/comptes/liste", name="user_comptes", methods={"GET"})
     * @Route("/gestion/compte/{id}", name="user_compte", methods={"GET"})
     * @IsGranted({"ROLE_admin-Principal","ROLE_admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function listerUserCompt(UserCompteActuelRepository $repo, SerializerInterface $serializer,UserInterface $userConnecte,UserCompteActuel $userCompte=null,$id=null)
    {
        
        if($id && !$userCompte instanceof UserCompteActuel) {
            throw new HttpException(404,'Resource non trouvée ! ');
        }
        elseif(!$userCompte){
            $userCompte=$repo->findByEntreprise($userConnecte->getEntreprise());
        }
        
        $data = $serializer->serialize($userCompte,'json',[ $this->groups => [$this->listUserCmpt]]);//chercher une alternative pour les groupes avec forest
        return new Response($data,200);
    }
     /**
     * @Route("/user/{id}", name="listeUser", methods={"GET"})
     * @IsGranted({"ROLE_Super-admin","ROLE_admin-Principal","ROLE_admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function listerUser(SerializerInterface $serializer,Utilisateur $user,UserInterface $userConnecte)
    {
        
        if(!$user instanceof Utilisateur) {//regler le cas du super admin qui liste les admins principaux
            throw new HttpException(404,'Resource non trouvée ! ');
        }
        elseif($user->getEntreprise()!=$userConnecte->getEntreprise()){
            throw new HttpException(404,'Cet utilisateur n\'est pas membre de votre entreprise ! ');
        }
        $data = $serializer->serialize($user,'json',[ $this->groups => ['list-user']]);//chercher une alternative pour les groupes avec forest
        return new Response($data,200);
    }
    /**
     * @Route("/lister/users", name="user_entreprise", methods={"GET"})
     * @IsGranted({"ROLE_Super-admin","ROLE_admin-Principal","ROLE_admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function listerLesUser(SerializerInterface $serializer,UserInterface $userConnecte,UtilisateurRepository $repo)
    {
        $entreprise=$userConnecte->getEntreprise();
        $users=$repo->findUserEntreprise($entreprise,$userConnecte);
        $data = $serializer->serialize($users,'json',[ $this->groups => ['list-user']]);//chercher une alternative pour les groupes avec forest
        return new Response($data,200);
    }
    /**
     * @Route("/contrat/{id}", name="contrat", methods={"GET"})
     * @IsGranted({"ROLE_Super-admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function contrat(Entreprise $entreprise){
        if(!$entreprise){
            throw new HttpException(404,'Ce partenaire n\'existe pas !');
        }
        elseif($entreprise->getRaisonSociale()=='SA Transfert' || $entreprise->getRaisonSociale()=='Etat du Sénégal'){
            throw new HttpException(404,'Ces contrats sont pour les partenaires de la SA Transfert uniquement !');
        }
        return [
            'Type de contrat'=>'CONTRAT DE PRESTATION DE SERVICE DE TRANSFERT D\'ARGENT',
            'Contenu'=>"CONTRAT DE PRESTATION DE SERVICE DE TRANSFERT D'ARGENT
                Cet accord est fait le ".date("d-m-Y")." à Dakar
                Par et entre la société SA Transfert  (le fournisseur) et la société ".$entreprise->getRaisonSociale()." (le client).
                PRÉAMBULE :
                CONSIDÉRANT QUE, le fournisseur fournit des services de transactions monétaires aux clients par l’intermédiaire du réseau du fournisseur , et considérant que  le client souhaite effectuer des opérations d’envoi et de retrait d’argent pour le compte de sa clientèle grâce à la plateforme du fournisseur.
                MAINTENANT, ET PAR CONSÉQUENT, dans la considération des engagements et des accords antérieurs et mutuels ci-dessus explicités, les parties, ainsi légalement liées, conviennent mutuellement comme suit :
                DÉFINITIONS ET INTERPRÉTATION
                Dans cet accord, à moins que le contexte n’exige autrement, les mots suivants et les expressions auront les significations suivantes :
                « Espèces » fait référence aux billets et pièces de monnaie relatifs à la devise... ;
                « Date effective » signifie, nonobstant la date de signature des accords définitifs, la date de signature ;
                « Service du fournisseur » signifie les services de transfert d’argent fournis par la SA Transfert ;
                « Code de transaction » signifie le code envoyé au bénéficiaire pour lui permettre de retirer le montant transféré;
                DURÉE DE L’ACCORD
                Cette accord à une durée de 5 ans à partir de la date effective et renouvelable par les partis concernés.
                OPÉRATION ET PORTÉE
                Ce présent contrat donne au client le droit d’utiliser la plateforme mise en place par la société SA Transfert dans le but d’effectuer des opérations d’envois et de retraits pour le compte de sa clientèle.  
                Il est formellement interdit au client de signer des accords de sous-traitance basé sur ce dit contrat sans en avisé le fournisseur.
                Les transactions vont se déroulées comme suit :
                En cas d’envoi,
                Après encaissement du montant  à envoyer et des frais (en espèce), le client devra procéder à  l’enregistrement des informations de l’envoyeur et de ceux du bénéficiaires. Un message sera dès lors envoyé à ces derniers pour leurs communiqués certaines informations sur la transaction.
                Le compte du client sera débiter du montant de l’opération ainsi que de 80% des frais payés par l’envoyeur (les 20% représentant la commission du client).
                En cas de retrait,
                Les retraits seront effectués sur la base du code de transaction présenté par le bénéficiaire, si celui ci est valide le client devra compléter les informations du bénéficiaire avant de procéder au paiement. Après cela, le compte du client sera crédité du montant retiré et de 10% des frais payés par l’envoyeur.
                NB : En cas d’annulation, seul le montant transféré sera remboursé, les frais encaissés sont non remboursable.
                CONFIDENTIALITÉ
                Il est formellement interdit au client de communiquer les données de sa clientèle (numéro de téléphone, numéro de pièce d’identité, montant envoyé ou reçu…). Le non respect de cette close peut faire l’objet de poursuit civile et pénale.
                PROPRIÉTÉ INTELLECTUELLE :
                Il est formellement interdit au client de copier la technologie du fournisseur, en cas de plagiat, des sanctions civiles, pénales et pécuniaires (dommages et intérêts) seront encourus par le dit client.
                SUSPENSION
                Le fournisseur à le droit de bloquer à tout moment le compte du client pour les causes suivantes :
                - manque d’étique
                - fraude
                - cas de blanchiment d’argent ou de financement d’activité terroriste
                - Et pour tout autre cause pouvant entraîner la rupture du contrat liant le client et le fournisseur
                INDEMNITÉ
                Le client accepte ici d’indemniser le fournisseur pour tout conflit légal prenant place en raison de l’abus du service par le client.
                AUTRES :
                - Le client sera assigné un code qui correspondra au numéro de compte du client.
                - Si besoin le client peut demander l’ouverture d’autres comptes à son nom.
                - Pour effectuer des transactions le client devra déposer l’argent sur le(s) compte(s) tenu par le fournisseur.
                - Le fournisseur devra alors délivrer le montant sur le compte du client équivalent au paiement en espèces dans un délai de six (6) heures suivant la transaction.
                - L’envoyeur et le bénéficiaire seront informés des éléments de la transaction par un message texte émanant du fournisseur."

        ];
    }
    /**
     * @Route("/compte/user/{id}", name="userCompte", methods={"GET"})
     * @IsGranted({"ROLE_Super-admin","ROLE_admin-Principal","ROLE_admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function userCompte(SerializerInterface $serializer,UserCompteActuelRepository $repo,Utilisateur $user){
        $userComp=$repo->findUserComptActu($user);
        $data = $serializer->serialize($userComp,'json',[ $this->groups => [$this->listUserCmpt]]);//chercher une alternative pour les groupes avec forest
        return new Response($data,200);
    }
    /**
     * @Route("/comptes/affecte/user/{id}", name="userCompteAffecte", methods={"GET"})
     * @IsGranted({"ROLE_Super-admin","ROLE_admin-Principal","ROLE_admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function userComptesAffecte(SerializerInterface $serializer,UserCompteActuelRepository $repo,Utilisateur $user){
        $userComp=$repo->findUserComptesAff($user);
        $data = $serializer->serialize($userComp,'json',[ $this->groups => [$this->listUserCmpt]]);//chercher une alternative pour les groupes avec forest
        return new Response($data,200);
    }
    /**
     * @Route("/compte/Mesdepots", name="showDepotCompte", methods={"POST"})
     * @IsGranted({"ROLE_Super-admin","ROLE_Caissier"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function showDepotCompte(Request $request, UserInterface $userConnecte, SerializerInterface $serializer,DepotRepository $repoDepot,CompteRepository $repoCompte){
        $data=json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();//si non json
        }
        
        if($compte=$repoCompte->findOneBy(['numeroCompte'=>$data["numeroCompte"]])){
            
            if($compte->getEntreprise()->getRaisonSociale()==$this->saTransfert){
                throw new HttpException(403,'On ne peut pas faire de depot dans le compte de SA Transfert !');
            }
        }
        else{
            throw new HttpException(404,'Ce numero de compte n\'existe pas !');
        }
        $depots=$repoDepot->findMesDepots($userConnecte,$compte);
        $data = $serializer->serialize($depots,'json',[ $this->groups => ['list-depot']]);
        return new Response($data,200);
    }
    /**
     * @Route("/entreprise/responsable/{id}", name="adminPartenaire", methods={"GET"})
     * @IsGranted({"ROLE_Super-admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function getResponsable(SerializerInterface $serializer,Entreprise $entreprise,UtilisateurRepository $repo){
        $userComp=$repo->findResponsable($entreprise);
        $data = $serializer->serialize($userComp,'json',[ $this->groups => ['list-user']]);//chercher une alternative pour les groupes avec forest
        return new Response($data,200);
    }
    /**
     * @Route("/compte/numeroCompte", name="leCompte", methods={"POST"})
     * @IsGranted({"ROLE_Super-admin","ROLE_Caissier"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function getLeCompte(Request $request, SerializerInterface $serializer,CompteRepository $repo){
        $data=json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();//si non json
        }
        
        if($compte=$repo->findOneBy(['numeroCompte'=>$data["numeroCompte"]])){
            
            if($compte->getEntreprise()->getRaisonSociale()==$this->saTransfert){
                throw new HttpException(403,'On ne peut pas faire de depot dans le compte de SA Transfert !');
            }
        }
        else{
            throw new HttpException(404,'Ce numero de compte n\'existe pas !');
        }
        $data = $serializer->serialize($compte,'json',[ $this->groups => ["list-compte"]]);
        return new Response($data,200);
    }
}
