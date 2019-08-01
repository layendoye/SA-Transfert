<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractFOSRestController
{
    /**
     * @Route("/inscription", name="inscription", methods={"POST"})
     */
    public function inscriptionUtilisateur(Request $request,ObjectManager $manager,UserPasswordEncoderInterface $encoder, UserInterface $Userconnecte, ProfilRepository $repoProfil,  ValidatorInterface $validator){
        /*
          Début variable utilisé frequement 
        */
        $roleSupAdmi=['ROLE_Super-admin'];
        $roleCaissier=['ROLE_Caissier'];
        $roleAdmiPrinc=['ROLE_admin-Principal'];
        $roleAdmi=['ROLE_admin'];
        $utilisateur=['ROLE_utilisateur'];
        /*
          Fin variable utilisé frequement 
        */
        
        $user=new Utilisateur();
        $form=$this->createForm(UtilisateurType::class,$user);
        
        $data=json_decode($request->getContent(),true);//Récupère une chaîne encodée JSON et la convertit en une variable PHP
        $profil=$data['Profil'];
        unset($data['Profil']);
        $form->submit($data);
        if(!$user->getEntreprise()){
            return $this->handleView($this->view(['Erreur' => 'Cet entreprise n\'existe pas !'],Response::HTTP_CONFLICT));
        }
        elseif($form->isSubmitted() && $form->isValid()){
            $hash=$encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            if(!$profil=$repoProfil->find($profil)){
                return $this->handleView($this->view(['Erreur' => 'Ce profil n\'existe pas !'],Response::HTTP_CONFLICT));
            }
            
            
            $libelle=$profil->getLibelle();
            $roles=['ROLE_'.$libelle];

            $roleUserConnecte[]=$Userconnecte->getRoles()[0];//on le met dans un tableau pour le comparer aux roles (qui sont des tableaux), le [1] est le role user par defaut
            
            if($roles == $roleSupAdmi   && $roleUserConnecte != $roleSupAdmi   ||
               $roles == $roleCaissier  && $roleUserConnecte != $roleSupAdmi   ||
               $roles == $roleAdmiPrinc && $roleUserConnecte != $roleSupAdmi   ||
               $roles == $roleAdmi      && $roleUserConnecte != $roleAdmiPrinc ||
               $roles == $utilisateur   && $roleUserConnecte != $roleAdmiPrinc
            ){
                return $this->handleView($this->view(['impossible' => 'Votre profil ne vous permet pas de créer ce type d\'utilisateur'],Response::HTTP_CONFLICT));
            }
            else{
                $user->setRoles($roles);
                if($roles!=$roleAdmiPrinc){//car si c'est l'admin principal on devra recuperer l'id de l'entreprise qui est sur le formulaire
                    $user->setEntreprise($Userconnecte->getEntreprise());//si ajout caissier il sera dans la même entreprise que le super-admin, si admin ou utilisateur il sera dans la même entreprise que l'admin-principal qui les a créé
                }
            }
            $user->setStatus('Actif');

            $manager->persist($user);
            $manager->flush();
            return $this->handleView($this->view(['status'=>'ok'],Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($validator->validate($form)));
    }
    /**
     *@Route("/connexion", name="connexion", methods={"POST"})
     */
    public function login(){ /*gerer dans config packages security.yaml*/}
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