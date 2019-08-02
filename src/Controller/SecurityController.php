<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\ProfilRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Entreprise;

class SecurityController extends AbstractFOSRestController
{
    /**
     * @Route("/inscription", name="inscription", methods={"POST"})
     * @Route("/add/admin-partenaire/{id}", name="add_adminPrinc", methods={"POST"})
     */
    public function inscriptionUtilisateur(Request $request,ObjectManager $manager,UserPasswordEncoderInterface $encoder, UserInterface $Userconnecte, ProfilRepository $repoProfil,  ValidatorInterface $validator,Entreprise $entreprise=null){
        /* Début variable utilisé frequement */
            $roleSupAdmi=['ROLE_Super-admin'];
            $roleCaissier=['ROLE_Caissier'];
            $roleAdmiPrinc=['ROLE_admin-Principal'];
            $roleAdmi=['ROLE_admin'];
            $utilisateur=['ROLE_utilisateur'];
            $erreur='Erreur';
        /* Fin variable utilisé frequement */
          
        $user=new Utilisateur();
        /* Début traitement formulaire et envoie des données */

            $form=$this->createForm(UtilisateurType::class,$user);
            //$data=json_decode($request->getContent(),true);//Récupère une chaîne encodée JSON et la convertit en une variable PHP
            $data=$request->request->all();
            $profil=$data['profil'];
            unset($data['profil']);//on supprime le profil car il ne fait pas partit de UtilisateurType
            $form->submit($data);

        /* Fin traitement formulaire et envoie des données */
        if($form->isSubmitted() && $form->isValid()){

            /* Début controle de saissie des profils*/

                if(!$profil=$repoProfil->find($profil)){
                    return $this->handleView($this->view([$erreur => 'Ce profil n\'existe pas !'],Response::HTTP_CONFLICT));
                }

            /* Début controle de saissie des profils*/
            
            /* Début gestion des roles pouvant ajouter */

                $roleUserConnecte[]=$Userconnecte->getRoles()[0];//on le met dans un tableau pour le comparer aux roles (qui sont des tableaux), le [1] est le role user par defaut
                $libelle=$profil->getLibelle();
                $roles=['ROLE_'.$libelle];

                if($roles == $roleSupAdmi   && $roleUserConnecte != $roleSupAdmi   ||
                $roles == $roleCaissier  && $roleUserConnecte != $roleSupAdmi   ||
                $roles == $roleAdmiPrinc && $roleUserConnecte != $roleSupAdmi   ||
                $roles == $roleAdmi      && $roleUserConnecte != $roleAdmiPrinc ||
                $roles == $utilisateur   && $roleUserConnecte != $roleAdmiPrinc
                ){//Vérifier que son profil lui permet de l'ajouter
                    return $this->handleView($this->view([$erreur => 'Votre profil ne vous permet pas de créer ce type d\'utilisateur'],Response::HTTP_CONFLICT));
                }
                $user->setRoles($roles);

            /* Fin gestion des roles pouvant ajouter */

            /* Début gestion des entreprises */
                
                if($entreprise && $libelle!='Caissier' && $libelle!='Super-admin'){//pour ajouter l'admin principale du partenaire
                    $user->setEntreprise($entreprise);
                }
                elseif($Userconnecte->getEntreprise()->getRaisonSociale()=='SA Transfert' && $roles == $roleSupAdmi || 
                       $Userconnecte->getEntreprise()->getRaisonSociale()=='SA Transfert' && $roles == $roleCaissier|| 
                    $roleUserConnecte == $roleAdmiPrinc && $roles == $roleAdmi  
                ){// si le Super-admin ajoute un caissier ou si l'admin principal ajout un admin (ou s il ajout un autre super-admin)
                    $user->setEntreprise($Userconnecte->getEntreprise());
                }
                elseif(!$entreprise && $libelle=='admin-Principal'){
                    return $this->handleView($this->view([$erreur => 'Cette entreprise n\'existe pas !!' ],Response::HTTP_CONFLICT));
                }
            
            /* Fin gestion des entreprises */

            /* Début gestion des compte */
                if($roles == $utilisateur && !$user->getCompte()){
                    return $this->handleView($this->view([$erreur => 'Aucun compte n\'est attribuer à cet utilisateur'],Response::HTTP_CONFLICT));
                }
                elseif($user->getCompte() && $user->getCompte()->getEntreprise()!=$Userconnecte->getEntreprise()){
                    return $this->handleView($this->view([$erreur => 'Ce compte n\'appartient pas à votre entreprise !!'],Response::HTTP_CONFLICT));
                }
                elseif($user->getCompte() && $libelle!='utilisateur'){
                    return $this->handleView($this->view([$erreur => 'Ce profil ne doit pas être rattacher à un compte !!'],Response::HTTP_CONFLICT));
                }
            /* Fin gestion des compte */

            /*Début gestion des images */
                $file=$request->files->all()['image'];
                $fileName=md5(uniqid()).'.'.$file->guessExtensionn();
                $user->setImage($fileName);
                $file->move($this->getParameter('image_directory'),$fileName);
            /*Début gestion des images */

            /* Début finalisation de l'inscription (status, mot de passe, enregistrement définitif) */
                $user->setStatus('Actif');
                $hash=$encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();
            /* Début finalisation de l'inscription (status, mot de passe, enregistrement définitif) */
            return $this->handleView($this->view(['status'=>'Enregistrer'],Response::HTTP_CREATED));
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