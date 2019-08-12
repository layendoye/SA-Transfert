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
    public function __construct()
    {
        $this->actif="Actif";
        $this->message="message";
        $this->status="status";
        $this->saTransfert="SA Transfert";
        $this->groups='groups';
        $this->contentType='Content-Type';
        $this->utilisateurStr='utilisateur';
        $this->compteStr='compte';
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
            $entreprise=$repo->findAll();
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
               'Compte partenaire' =>'Le compte numéro '.$compte->getNumeroCompte().' lui a été assigné'
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
        $compte='compte';
        $depot = new Depot();
        $form = $this->createForm(DepotType::class, $depot);
        $data=json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();//si non json
        }
        if($compte=$repo->findOneBy(['numeroCompte'=>$data[$compte]])){
            $data[$compte]=$compte->getId();//on lui donne directement l'id
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
    public function bloqueEntrep(ObjectManager $manager,Entreprise $entreprise=null )
    {
        if(!$entreprise){
            throw new HttpException(404,'Ce partenaire n\'existe pas !');
        }
        elseif($entreprise->getRaisonSociale()==$this->saTransfert){
            throw new HttpException(403,'Impossible de bloquer SA Transfert !');
        }
        elseif($entreprise->getStatus() == $this->actif){
            $entreprise->setStatus("bloqué");
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
            $user->setStatus("bloqué");
            $texte='Bloqué';
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
            'Numéro de compte '=> $compte->getNumeroCompte()
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
        
        $data = $serializer->serialize($userCompte,'json',[ $this->groups => ['list-userCmpt']]);//chercher une alternative pour les groupes avec forest
        return new Response($data,200);
    }
     /**
     * @Route("/user/{id}", name="user_entreprise", methods={"GET"})
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
}
