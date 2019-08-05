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

class EntrepriseController extends AbstractFOSRestController
{
    private $actif;
    private $statut;
    private $message;
    public function __construct(){
        $this->actif='Actif';
        $this->statut='status';
        $this->message='message';
    }
    
    /**
     * @Route("/entreprises/liste", name="entreprises", methods={"GET"})
     * @Route("/entreprise/{id}", name="entreprise", methods={"GET"})
     */
    public function lister(EntrepriseRepository $repo, SerializerInterface $serializer,Entreprise $entreprise=null,$id=null)
    {
        
        if($id && !$entreprise instanceof Entreprise) {
            throw new HttpException(404,'Ce partenaire n\'existe pas !');
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
            if($compte->getEntreprise()->getRaisonSociale()=='SA Transfert'){
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
               'message' => 'Le depot a bien été effectué dans le compte '.$compte->getNumeroCompte()
           ];
           return $this->handleView($this->view($afficher,Response::HTTP_CREATED));

        }
        return $this->handleView($this->view($validator->validate($form)));
    }

    /**
    * @Route("/bloque/entreprises/{id}", name="bloque_entreprise", methods={"GET"})
    */ 
    public function bloque(ObjectManager $manager,Entreprise $entreprise=null )
    {
        if(!$entreprise){
            throw new HttpException(404,'Ce partenaire n\'existe pas !');
        }
        elseif($entreprise->getRaisonSociale()=='SA Transfert'){
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
    * @Route("/nouveau/compte/{id}", name="bloque_entreprise", methods={"GET"})
    */ 
    public function addCompte(ObjectManager $manager, Entreprise $entreprise){
        $compte =new Compte();
        if(!$entreprise){
            throw new HttpException(404,'Ce partenaire n\'existe pas !');
        }
        elseif($entreprise->getRaisonSociale()=='SA Transfert'){
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
}
