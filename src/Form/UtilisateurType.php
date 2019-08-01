<?php

namespace App\Form;

use App\Entity\Compte;
use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password')
            ->add('confirmPassword')
            ->add('nom')
            ->add('email')
            ->add('telephone')
            ->add('nci')
            ->add('image')
            ->add('compte', EntityType::class, ['class'=> Compte::class,
                'choice_label' => function(Compte $compte,UserInterface $Userconnecte) {
                    if($compte->getEntreprise()==$Userconnecte->getEntreprise()){
                       return $compte->getNumeroCompte(); 
                    }
            }]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'csrf_protection'=>false
        ]);
    }
}
