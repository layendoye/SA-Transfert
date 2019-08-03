<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Repository\EntrepriseRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntrepriseController extends AbstractFOSRestController
{
    /**
     * @Route("/entreprises/liste", name="entreprises")
     * @Route("/entreprise/{id}", name="entreprise")
     */
    public function lister(EntrepriseRepository $repo, SerializerInterface $serializer,Entreprise $entreprise=null)
    {
        if(!$entreprise){
            $entreprise=$repo->findAll();
        }
        $data = $serializer->serialize($entreprise,'json',['groups' => ['list']]);//chercher une alternative pour les groupes avec forest
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
            $entreprise->setStatus('Actif');
            $manager->persist($entreprise);
            $manager->flush();
            $compte=new Compte();
            $compte->setNumeroCompte(date('y').date('m').' '.date('d').date('H').' '.date('i').date('s').' '.$entreprise->getId())
                   ->setEntreprise($entreprise);

            $manager->persist($compte);
            $manager->flush();
            return $this->handleView($this->view(['status'=>'Enregistrer'],Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($validator->validate($form)));
    }

}
