<?php

namespace App\Form;

use App\Entity\Depot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
 use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class DepotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom_depot', TextType::class, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'Nom du dépôt'],
            'label' => 'Nom du dépôt',
            'label_attr' => ['class' => 'form-label'],
            "mapped"=>true,
            'empty_data' => ''
        ])
        ->add('localisation_depot', TextType::class, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'Adresse'],
            'label' => 'Localisation',
            'label_attr' => ['class' => 'form-label'],
            "mapped"=>true,
            'empty_data' => ''

        ])
        ->add('capacite_depot', NumberType::class, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'Capacité'],
            'label' => 'Capacité',
            'label_attr' => ['class' => 'form-label'],
            "mapped"=>true,
            'empty_data' => 0
        ])
        ->add('unite_cap_depot', ChoiceType::class, [
            'choices' => [
                'Kilogrammes (kg)' => 'kg',
                'Litres (L)' => 'L',
                'Mètres cubes (m³)' => 'm3',
                'Palettes' => 'palettes',
             ],
            'attr' => ['class' => 'form-select'],
            'label' => 'Unité de capacité',
            'label_attr' => ['class' => 'form-label'],
            "mapped"=>true,
            'empty_data' => ''

        ])
        ->add('type_stockage_depot', ChoiceType::class, [
            'choices' => [
                'Température ambiante' => 'temp_ambiante',
                'Réfrigéré (0 à 8°C)' => 'refrigere',
                'Congelé (-18°C et moins)' => 'congele',
                'Chambre froide négative (-25°C à -30°C)' => 'chambre_froide_negative',
                'Chambre froide positive (0 à 4°C)' => 'chambre_froide_positive',
                'Atmosphère contrôlée' => 'atmosphere_controlee',
                'Stockage en environnement sec' => 'stockage_sec',
            ],
            'attr' => ['class' => 'form-select'],
            'label' => 'Type de stockage',
            'label_attr' => ['class' => 'form-label'],
            "mapped"=>true,
            'empty_data' => ''

                    ])
        ->add('statut_depot', ChoiceType::class, [
            'choices' => [
                'Actif' => 'actif',
                'Inactif' => 'inactif',
                'En maintenance' => 'en_maintenance',
                'Plein' => 'plein',
            ],
            'attr' => ['class' => 'form-select'],
            'label' => 'Statut',
            'label_attr' => ['class' => 'form-label'],
            "mapped"=>true,
            'empty_data' => ''

        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Depot::class,
        ]);
    }
}
