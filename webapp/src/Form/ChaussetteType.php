<?php

namespace App\Form;

use App\Entity\Chaussette;
use App\Entity\Couleur;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChaussetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Commentaire')
            ->add('NomChaussette')
            ->add('Statut')
            ->add('taille')
            ->add('couple')
            ->add('dateCreation')
            ->add('couleur', EntityType::class, [
                'class' => Couleur::class,
                'choice_label' => 'id',
            ])
            ->add('relation', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'id',
            ])
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chaussette::class,
        ]);
    }
}
