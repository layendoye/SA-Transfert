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

class EntrepriseController extends AbstractFOSRestController
{
    private $actif;
    private $statut;
    private $message;
    private $saTransfert;
    public function __construct(){
        $this->actif='Actif';
        $this->statut='status';
        $this->message='message';
        $this->saTransfert='SA Transfert';
    }
    
    /**
     * @Route("/entreprises/liste", name="entreprises", methods={"GET"})
     * @Route("/entreprise/{id}", name="entreprise", methods={"GET"})
     */
    public function lister(EntrepriseRepository $repo, SerializerInterface $serializer,Entreprise $entreprise=null,$id=null)
    {
        
        if($id && !$entreprise instanceof Entreprise) {
            throw new HttpException(404,'Ce partenaire n\'existe pas!');
        }
        if(!$entreprise){
            $entreprise=$repo->findAll();
        }
        $data = $serializer->serialize($entreprise,'json',['groups' => ['list-entreprise']]);//chercher une alternative pour les groupes avec forest
        return new Response($data,200,['Content-Type' => 'application/json']);
    }
    /**
     * @Route("/partenaires/add", name="add_entreprise", methods={"POST"})
     */
    public function add(Request $request, ObjectManager $manager, ValidatorInterface $validator,UserPasswordEncoderInterface $encoder)
    {
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
        unset($data[$raisonSociale],$data[$ninea],$data[$adresse],$data[$telephoneEntreprise],$data[$emailEntreprise]);//on supprime les données du partenaire
        $user=new Utilisateur();
        $form2=$this->createForm(UtilisateurType::class,$user);
        $form2->submit($data);
        if(!$form2->isSubmitted() || !$form2->isValid()){
            return $this->handleView($this->view($validator->validate($form2)));
        }

        $entreprise->setStatus($this->actif); 
        $compte=new Compte();
        $compte->setNumeroCompte(date('y').date('m').' '.date('d').date('H').' '.date('i').date('s'))
               ->setEntreprise($entreprise);
        $manager->persist($entreprise);            
        $manager->persist($compte);
        
        $user->setRoles(['ROLE_admin-Principal'])
            ->setEntreprise($entreprise)
            ->setStatus($this->actif);
        $hash=$encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($hash);
        /*Début gestion des images */
            if($requestFile=$request->files->all()){
                $file=$requestFile['image'];
                if($file->guessExtension()!='png' && $file->guessExtension()!='jpeg' ){
                    throw new HttpException(400,'Entrer une image valide !! ');
                }
                
                $fileName=md5(uniqid()).'.'.$file->guessExtension();//on change le nom du fichier
                $user->setImage($fileName);
                $file->move($this->getParameter('image_directory'),$fileName); //definir le image_directory dans service.yaml
            }
        /*Début gestion des images */
        $manager->persist($user);

        $manager->flush();
        $afficher = [
           $this->statut => 201,
           $this->message => 'Le partenaire '.$entreprise->getRaisonSociale().' ainsi que son admin principal ont bien été ajouté !! ',
           'Compte partenaire' =>'Le compte numéro '.$compte->getNumeroCompte().' lui a été assigné'
        ];
        return $this->handleView($this->view($afficher,Response::HTTP_CREATED));
    }

    /**
    * @Route("/nouveau/depot", methods={"POST"})
    */
    public function depot (Request $request, ValidatorInterface $validator, UserInterface $Userconnecte,CompteRepository $repo, ObjectManager $manager)
    {
        $depot = new Depot();
        $form = $this->createForm(DepotType::class, $depot);
        $data=json_decode($request->getContent(),true);
        if($compte=$repo->findOneBy(['numeroCompte'=>$data['compte']]))
        {
            $data['compte']=$compte->getId();//on lui donne directement l'id
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
               $this->statut => 201,
               $this->message => 'Le depot a bien été effectué dans le compte '.$compte->getNumeroCompte()
           ];
           return $this->handleView($this->view($afficher,Response::HTTP_CREATED));

        }
        return $this->handleView($this->view($validator->validate($form)));
    }

    /**
    * @Route("/bloque/entreprises/{id}", name="bloque_entreprise", methods={"GET"})
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
        $afficher = [$this->statut => 200,$this->message => $texte];
        return $this->handleView($this->view($afficher,Response::HTTP_OK));
    }
    /**
    * @Route("/bloque/user/{id}", name="bloque_user", methods={"GET"})
    * @IsGranted({"ROLE_Super-admin","ROLE_admin-Principal","ROLE_admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
    */ 
    public function bloqueUser(UserInterface $Userconnecte,ObjectManager $manager,Utilisateur $user=null)
    {
        if(!$user){
            throw new HttpException(404,'Cet utilisateur n\'existe pas !');
        }
        if($user==$Userconnecte){
            throw new HttpException(403,'Impossible de se bloquer soit même !');
        }
        if(!$entreprise=$user->getEntreprise()){//s il n'existe pas donc c est un user simple (pas d entreprise car on l a rattacher avec compte)
            $entreprise=$user->getCompte()->getEntreprise();
        }

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
        $afficher = [$this->statut => 200,$this->message => $texte];
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
            $this->statut => 201,
            $this->message => 'Un nouveau compte est créé pour l\'entreprise '.$entreprise->getRaisonSociale(),
            'Numéro de compte '=> $compte->getNumeroCompte()
        ];
        return $this->handleView($this->view($afficher,Response::HTTP_OK));
    }
    /**
     * @Route("/changer/compte" ,name="change_compte")
     * @IsGranted("ROLE_admin-Principal", statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function changeCompte(Request $request,ObjectManager $manager, UserInterface $Userconnecte,UtilisateurRepository $repoUser,CompteRepository $repoCompte)
    {//securiser la route
        $data=json_decode($request->getContent());
        if(!isset($data->utilisateur,$data->compte)){
            throw new HttpException(404,'Remplir l\'utilisateur et le compte !');
        }
        elseif(!$user=$repoUser->find($data->utilisateur)){
            throw new HttpException(404,'Cet utilisateur n\'existe pas !');
        }
        elseif(!$user->getCompte()){//vu qu en creant un user simple on lui affecte un compte, s'il n'en a pas donc c est pas un user
            throw new HttpException(403,'Impossible d\'affecter un compte à et utilisateur !');
        }
        elseif($user->getCompte()->getEntreprise()!=$Userconnecte->getEntreprise()){
            throw new HttpException(404,'Cet utilisateur n\'appartient pas à votre entreprise !');
        }
        if(!$compte=$repoCompte->find($data->compte)){
            throw new HttpException(404,'Ce compte n\'existe pas !');
        }
        elseif($compte->getEntreprise()!=$Userconnecte->getEntreprise()){
            throw new HttpException(404,'Ce compte n\'appartient pas à votre entreprise !');
        }
        $user->setCompte($compte);
        $manager->persist($user);
        $manager->flush();
        $afficher = [
               $this->statut => 201,
               $this->message => 'Le compte de l\'utilisateur a été modifié !!'
           ];
        return $this->handleView($this->view($afficher,Response::HTTP_OK));
    }

     /**
     * @Route("/user/{id}", name="entreprise", methods={"GET"})
     */
    public function listerUser(SerializerInterface $serializer,Utilisateur $user=null)
    {
        
        $data = $serializer->serialize($user,'json',['groups' => ['list-entreprise']]);//chercher une alternative pour les groupes avec forest
        return new Response($data,200,['Content-Type' => 'application/json']);
    }
}
