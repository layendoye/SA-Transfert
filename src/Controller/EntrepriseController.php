<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Compte;
use App\Form\DepotType;
use App\Entity\Entreprise;
use App\Form\EntrepriseType;
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
    public function add(Request $request, ObjectManager $manager, ValidatorInterface $validator)
    {
        $entreprise = new Entreprise();
        $form=$this->createForm(EntrepriseType::class,$entreprise);
        $data=json_decode($request->getContent(),true);
        $form->submit($data);
        if($form->isSubmitted() && $form->isValid()){
            $entreprise->setStatus($this->actif);
            
            $compte=new Compte();
            $compte->setNumeroCompte(date('y').date('m').' '.date('d').date('H').' '.date('i').date('s'))
                   ->setEntreprise($entreprise);
            $manager->persist($entreprise);            
            $manager->persist($compte);
            $manager->flush();
            $afficher = [
               $this->statut => 201,
               $this->message => 'Le partenaire '.$entreprise->getRaisonSociale().' a bien été ajouté !! ',
               'Compte' =>'Le compte numéro '.$compte->getNumeroCompte().' lui a été assigné'
           ];
            return $this->handleView($this->view($afficher,Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($validator->validate($form)));
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
}
