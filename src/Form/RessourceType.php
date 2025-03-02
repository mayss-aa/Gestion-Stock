<?php

namespace App\Form;

use App\Entity\Depot;
use App\Entity\Ressource;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RessourceType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
         $builder
        ->add('nom_ressource', TextType::class, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le nom de la ressource'],
            'label' => 'Nom de la ressource',
            'label_attr' => ['class' => 'form-label'],
            "mapped"=>true ,            'empty_data' => ''

        ])
        ->add('type_ressource', TextType::class, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le type de la ressource'],
            'label' => 'Type de ressource',
            'label_attr' => ['class' => 'form-label'],
            "mapped"=>true ,            'empty_data' => ''

        ])
        ->add('quantite_ressource', NumberType::class, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez la quantité'],
            'label' => 'Quantité',
            'label_attr' => ['class' => 'form-label'],
            "mapped"=>true ,            'empty_data' => 0

        ])
        ->add('unite_mesure', ChoiceType::class, [
            'choices' => [
                'Kilogrammes (kg)' => 'kg',
                'Litres (L)' => 'L',
                
                'Mètres cubes (m³)' => 'm3',
            ],
            'attr' => ['class' => 'form-select'],
            'label' => 'Unité de mesure',
            'label_attr' => ['class' => 'form-label'],
            "mapped"=>true ,            'empty_data' => ''

        ])
        ->add('date_ajout_ressource', DateTimeType::class, [
            'widget' => 'single_text',
            'attr' => ['class' => 'form-control'],
            'label' => 'Date d\'ajout',
            'label_attr' => ['class' => 'form-label'],
            'data' => new \DateTime() ,
            "mapped"=>true ,            'empty_data' => ''

        ])
        ->add('date_expiration_ressource', DateTimeType::class, [
            'widget' => 'single_text',
             'attr' => ['class' => 'form-control'],
            'label' => 'Date d\'expiration',
            'label_attr' => ['class' => 'form-label'],
            'data' => (new \DateTime())->modify('+1 day'), // 🟢 Définit la date actuelle +1 jour
            "mapped"=>true ,            'empty_data' => ''


        ])
        ->add('statut_ressource', ChoiceType::class, [
            'choices' => [
                'Disponible' => 'disponible',
                'En rupture' => 'en_rupture',
                'Réservé' => 'reserve',
                'En cours de livraison' => 'en_cours_de_livraison',
                'Endommagé' => 'endommage',
                'Expiré' => 'expire',
                'En attente' => 'en_attente',
                'En réparation' => 'en_reparation',
            ],
            'attr' => ['class' => 'form-select'],
            'label' => 'Statut',
            'label_attr' => ['class' => 'form-label'],
            "mapped"=>true ,            'empty_data' => ''

        ])
        ->add('depot', EntityType::class, [
            'class' => Depot::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('d')
                    ->where('d.isshown = :status')
                    ->setParameter('status', true);
            },
            'attr' => ['class' => 'form-select'],
            'label' => 'Dépôt',
            'label_attr' => ['class' => 'form-label'],
            "mapped"=>true , 'empty_data' => ''
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ressource::class,
        ]);
    }
}
