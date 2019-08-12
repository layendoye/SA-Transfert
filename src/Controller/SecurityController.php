<?php

namespace App\Controller;


use App\Entity\Compte;
use App\Entity\Entreprise;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Entity\UserCompteActuel;
use App\Repository\CompteRepository;
use App\Repository\ProfilRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\UtilisateurRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
class SecurityController extends AbstractFOSRestController
{
    private $actif;
    private $message;
    private $status;
    private $saTransfert;
    private $image_directory;
    public function __construct()
    {
        $this->actif="Actif";
        $this->message="message";
        $this->status="status";
        $this->saTransfert="SA Transfert";
        $this->image_directory="image_directory";

    }
    /**
     * @Route("/inscription", name="inscription", methods={"POST"})
     * @IsGranted({"ROLE_Super-admin","ROLE_admin-Principal","ROLE_admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
     */
    public function inscriptionUtilisateur(Request $request,ObjectManager $manager,UserPasswordEncoderInterface $encoder, UserInterface $Userconnecte, ProfilRepository $repoProfil,  ValidatorInterface $validator,CompteRepository $repoComp){          
        
        #####################----------Début traitement formulaire et envoie des données----------#####################
            
            $user=new Utilisateur();
            $form = $this->createForm(UtilisateurType::class,$user);
            $data = json_decode($request->getContent(),true);//Récupère une chaîne encodée JSON et la convertit en une variable PHP
            if(!$data){//s il n'existe pas donc on recupere directement le tableau via la request
                $data=$request->request->all();
            }
            $form->submit($data);
            if(!$form->isSubmitted() ||!$form->isValid()){
                return $this->handleView($this->view($validator->validate($form)));
            }

        #####################-----------Fin traitement formulaire et envoie des données-----------#####################
        
        #####################----------------Début controle de saissie des profils----------------#####################
            
            $idProfil = $user->getProfil();# recuperer via le formulaire
            if(!$profil=$repoProfil->find($idProfil)){
                throw new HttpException(404,'Ce profil n\'existe pas !');
            }

        #####################-----------------Fin controle de saissie des profils-----------------#####################

        #####################----------------Début gestion des roles pouvant ajouter -------------#####################

            $roleUserConnecte[]=$Userconnecte->getRoles()[0];# on le met dans un tableau pour le comparer aux roles (qui sont des tableaux), le [1] est le role user par defaut
            $libelle=$profil->getLibelle();
            $roles=['ROLE_'.$libelle];
            $this->validationRole($roles,$roleUserConnecte);
            $user->setRoles($roles);

        #####################-----------------Fin gestion des roles pouvant ajouter --------------#####################

        #####################------------------------Début gestion des images --------------------#####################
            
            if($requestFile=$request->files->all()){
                $file=$requestFile['image'];
                $extension=$file->guessExtension();
                if($extension!='png' && $extension!='jpeg'){
                    throw new HttpException(400,'Entrer une image valide !! ');
                }
                
                $fileName=md5(uniqid()).'.'.$extension;//on change le nom du fichier
                $user->setImage($fileName);
                $file->move($this->getParameter($this->image_directory),$fileName); //definir le image_directory dans service.yaml
            }

        #####################-------------------------Fin gestion des images ---------------------#####################

        #####################------------------Début finalisation de l'inscription----------------#####################
            
            $user->setEntreprise($Userconnecte->getEntreprise());//si super admin ajout caissier (mm entreprise) si admin principal ajout admin ou user simple (mm entreprise)
            $user->setStatus($this->actif)
                 ->setEntreprise($Userconnecte->getEntreprise());
            $hash=$encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();
            return $this->handleView($this->view([$this->status=>'Enregistrer'],Response::HTTP_CREATED));

        #####################-------------------Fin finalisation de l'inscription-----------------#####################
    }

    /**
     * @Route("/user/update/{id}", name="update_user", methods={"POST"})
     */
    public function updateUser(Utilisateur $user,Request $request, ObjectManager $manager, ValidatorInterface $validator,UserPasswordEncoderInterface $encoder){
        #####################----------Début gestion formulaire---------------#####################
            
            if(!$user){
                throw new HttpException(404,'Cet utilisateur n\'existe pas !');
            }
            $form = $this->createForm(UtilisateurType::class,$user);
            $data=json_decode($request->getContent(),true);//si json
            if(!$data){
                $data=$request->request->all();//si non json
            }
            $ancienNom=$user->getImage();//pour le supprimer
            
            $form->submit($data);
            if(!$form->isSubmitted() || !$form->isValid()){
                return $this->handleView($this->view($validator->validate($form)));
            }

        #####################-----------Fin gestion formulaire----------------#####################
        
        #####################----------Début gestion des images --------------#####################
            
            if($requestFile=$request->files->all()){
                $file=$requestFile['image'];
                if($file->guessExtension()!='png' && $file->guessExtension()!='jpeg'){
                    throw new HttpException(400,'Entrer une image valide !! ');
                }

                $fileName=md5(uniqid()).'.'.$file->guessExtension();//on change le nom du fichier
                $user->setImage($fileName);
                $file->move($this->getParameter($this->image_directory),$fileName); //definir le image_directory dans service.yaml
                $ancienPhoto=$this->getParameter($this->image_directory)."/".$ancienNom;
                if($ancienNom){
                   unlink($ancienPhoto);//supprime l'ancienne 
                }
                
            }

        #####################-----------Fin gestion des images ---------------#####################

        #####################------Début finalisation de l'inscription--------#####################
            
            $hash=$encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user); 
            $manager->flush();
            $afficher = [
                $this->status => 200,
                $this->message => 'L\'utilisateur a été correctement modifié !'
            ];
            return $this->handleView($this->view($afficher,Response::HTTP_OK));
            
        #####################-------Fin finalisation de l'inscription---------#####################
    }

    /**
     *@Route("/connexion", name="connexion", methods={"POST"})
     */
    public function login(){ }

    public function validationRole($roles,$roleUserConnecte){
        $roleSupAdmi=['ROLE_Super-admin'];
        $roleCaissier=['ROLE_Caissier'];
        $roleAdmiPrinc=['ROLE_admin-Principal'];
        $roleAdmi=['ROLE_admin'];
        $utilisateur=['ROLE_utilisateur'];
        if($roles==$roleAdmiPrinc){
            throw new HttpException(403,'Impossible de créer ce type d\'utilisateur sans créer un nouveau partenaire');
        }
        elseif($roles == $roleSupAdmi  && $roleUserConnecte != $roleSupAdmi   ||
               $roles == $roleCaissier && $roleUserConnecte != $roleSupAdmi   ||
               $roles == $roleAdmi     && $roleUserConnecte != $roleAdmiPrinc ||
               $roles == $utilisateur  && $roleUserConnecte != $roleAdmiPrinc && $roleUserConnecte != $roleAdmi

        ){//Vérifier que son profil lui permet de l'ajouter
             throw new HttpException(403,'Votre profil ne vous permet pas de créer ce type d\'utilisateur');
        }
    }
}
     /*
        1 - Aller dans config -> packages -> fos_rest.yaml
        2 - Modifier le extend de cette classe par FOSRestController
        3 - Aller dans le UserType ajouter 'csrf_protection'=>false

        Pour authentification
        1 - Aller dans le fichier security.yaml
        2 - installer le bundle : composer require lexik/jwt-authentication-bundle
        3 - Lancer : mkdir -p config/jwt
        4 - Puis : openssl genrsa -out config/jwt/private.pem -aes256 4096
        5 - Un mot de passe et on confirme
        6 - Ensuite : openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
        
    */