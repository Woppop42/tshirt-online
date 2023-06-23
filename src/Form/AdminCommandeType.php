<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AdminCommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            #->add('quantite')
            ->add('etat', ChoiceType::class, [
                "choices" => [
                    'En cours de traitement' => 'En cours de traitement',
                    'Livraison en cours' => 'Livraison en cours',
                    'Commande finalisée' => 'Commande finalisée',
                    'Annulée' => 'Annulée'
                ]
            ])
           ->add('prix')
            #->add('date_enregistrement')
           # ->add('produit_id')
            #->add('user')
            #->add('produit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
