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

class SecurityController extends AbstractFOSRestController
{
    /**
     * @Route("/inscription", name="inscription", methods={"POST"})
     */
    public function inscriptionUtilisateur(Request $request,ObjectManager $manager,UserPasswordEncoderInterface $encoder, UserInterface $Userconnecte, ProfilRepository $repoProfil,  ValidatorInterface $validator,CompteRepository $repoComp){
      /* Début variable utilisé frequement */   
        $roleSupAdmi=['ROLE_Super-admin'];
        $roleCaissier=['ROLE_Caissier'];
        $roleAdmiPrinc=['ROLE_admin-Principal'];
        $roleAdmi=['ROLE_admin'];
        $utilisateur=['ROLE_utilisateur'];
      /* Fin variable utilisé frequement */
          
        $user=new Utilisateur();
        
       /* Début traitement formulaire et envoie des données */

        $form = $this->createForm(UtilisateurType::class,$user);
        $data = json_decode($request->getContent(),true);//Récupère une chaîne encodée JSON et la convertit en une variable PHP
        if(!$data){//s il n'existe pas donc on recupere directement le tableau via la request
            $data=$request->request->all();
        }
        $form->submit($data);
        
       /* Fin traitement formulaire et envoie des données */
        
        if($form->isSubmitted() && $form->isValid()){
            $idProfil = $user->getProfil();
            $idCompte = $user->getCompte();
            $compte=$repoComp->find($idCompte);
            
           /* Début controle de saissie des profils*/

            if(!$profil=$repoProfil->find($idProfil)){
                throw new HttpException(404,'Ce profil n\'existe pas !');
            }

           /* Fin controle de saissie des profils*/
            
           /* Début gestion des roles pouvant ajouter */

            $roleUserConnecte[]=$Userconnecte->getRoles()[0];//on le met dans un tableau pour le comparer aux roles (qui sont des tableaux), le [1] est le role user par defaut
            $libelle=$profil->getLibelle();
            $roles=['ROLE_'.$libelle];
            if($roles==$roleAdmiPrinc){
                throw new HttpException(403,'Impossible de créer ce type d\'utilisateur sans créer un nouveau partenaire');
            }
            elseif($roles == $roleSupAdmi  && $roleUserConnecte != $roleSupAdmi   ||
                   $roles == $roleCaissier && $roleUserConnecte != $roleSupAdmi   ||
                   $roles == $roleAdmi     && $roleUserConnecte != $roleAdmiPrinc && $roleUserConnecte != $roleAdmi ||
                   $roles == $utilisateur  && $roleUserConnecte != $roleAdmiPrinc && $roleUserConnecte != $roleAdmi

            ){//Vérifier que son profil lui permet de l'ajouter
                 throw new HttpException(403,'Votre profil ne vous permet pas de créer ce type d\'utilisateur');
            }
            $user->setRoles($roles);

           /* Fin gestion des roles pouvant ajouter */

           /* Début gestion des compte */
            if ( $idProfil==5 && $idCompte && !$compte instanceof Compte ) {//si on ajout un utilisateur et que le compte qu on veut lui assigné n'existe pas
                throw new HttpException(404,'Ce compte n\'existe pas !');
            }
            elseif($roles == $utilisateur && !$compte){
                throw new HttpException(404,'Aucun compte n\'est attribuer à cet utilisateur');
            }
            elseif($compte && $compte->getEntreprise()!=$Userconnecte->getEntreprise()){
                throw new HttpException(403,'Ce compte n\'appartient pas à votre entreprise !!');
            }
            elseif($compte && $libelle!='utilisateur'){
                throw new HttpException(403,'Ce profil ne doit pas être rattacher à un compte !!');
            }
           /* Fin gestion des compte */

           /*Début gestion des images */
            if($requestFile=$request->files->all()){
                $file=$requestFile['image'];
                
                if($file->guessExtension()!='png' && $file->guessExtension()!='jpeg'){
                    throw new HttpException(400,'Entrer une image valide !! ');
                }
                
                $fileName=md5(uniqid()).'.'.$file->guessExtension();//on change le nom du fichier
                $user->setImage($fileName);
                $file->move($this->getParameter('image_directory'),$fileName); //definir le image_directory dans service.yaml
            }
           /*Début gestion des images */

           /* Début finalisation de l'inscription (status, mot de passe, enregistrement définitif) */
            $user->setEntreprise($Userconnecte->getEntreprise());//si super admin ajout caissier (mm entreprise) si admin principal ajout admin ou user simple (mm entreprise)
            $user->setStatus('Actif')
                 ->setEntreprise($Userconnecte->getEntreprise());
            $hash=$encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $userCompte=new UserCompteActuel();

            $userCompte->setCompte($compte)
                    ->setUtilisateur($user)
                    ->setDateAffectation(new \DateTime());
            $manager->persist($userCompte);
            $manager->persist($user);
            $manager->flush();
           /* Début finalisation de l'inscription (status, mot de passe, enregistrement définitif) */
            return $this->handleView($this->view(['status'=>'Enregistrer'],Response::HTTP_CREATED));
        }
        
        return $this->handleView($this->view($validator->validate($form)));
    }
    /**
     * @Route("/user/update/{id}", name="update_user", methods={"POST"})
     */
    public function updateUser(Utilisateur $user,Request $request, ObjectManager $manager, ValidatorInterface $validator,UserPasswordEncoderInterface $encoder){
        if(!$user){
            throw new HttpException(404,'Cet utilisateur n\'existe pas !');
        }
        $form = $this->createForm(UtilisateurType::class,$user);
        $data=json_decode($request->getContent(),true);//si json
        if(!$data){
            $data=$request->request->all();//si non json
        }
        $ancienPhoto=$this->getParameter('image_directory')."/".$user->getImage();
        $form->submit($data);
        if(!$form->isSubmitted() || !$form->isValid()){
            return $this->handleView($this->view($validator->validate($form)));
        }
        if($requestFile=$request->files->all()){
            $file=$requestFile['image'];
            
            
            if($file->guessExtension()!='png' && $file->guessExtension()!='jpeg'){
                throw new HttpException(400,'Entrer une image valide !! ');
            }
            
            $fileName=md5(uniqid()).'.'.$file->guessExtension();//on change le nom du fichier
            $user->setImage($fileName);
            $file->move($this->getParameter('image_directory'),$fileName); //definir le image_directory dans service.yaml
            unlink($ancienPhoto);
        }
        $hash=$encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($hash);
        $manager->persist($user); 
        $manager->flush();
        $afficher = [
           'status' => 200,
           'message' => 'L\'utilisateur a été correctement modifié !'
        ];
        return $this->handleView($this->view($afficher,Response::HTTP_OK));
    }

    /**
     *@Route("/connexion", name="connexion", methods={"POST"})
     */
    public function login(){ }
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