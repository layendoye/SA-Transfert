<?php

namespace App\Form;

use App\Entity\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnvoieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomClientEmetteur')
            ->add('telephoneEmetteur')
            ->add('nciEmetteur')
            ->add('montant')
            ->add('nomClientRecepteur')
            ->add('telephoneRecepteur')
            //->add('frais')
            //->add('dateEnvoi')
            //->add('code')
            //->add('commissionEmetteur')
            //*->add('commissionRecepteur')
            //->add('commissionWari')
            //->add('taxesEtat')
            //->add('status')
            //->add('userComptePartenaireEmetteur')
            //->add('userComptePartenaireRecepteur')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
            'csrf_protection' =>false
        ]);
    }
}
