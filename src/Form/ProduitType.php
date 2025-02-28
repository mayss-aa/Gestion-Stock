<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_produit', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Le nom du produit est requis']),
                ],
            ])
            ->add('cycle_culture', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Le cycle de culture est requis']),
                ],
            ])
            ->add('quantite_produit', null, [
                'constraints' => [
                    new NotBlank(['message' => 'La quantité est requise']),
                ],
            ])
            ->add('unite_quant_prod', null, [
                'constraints' => [
                    new NotBlank(['message' => "L'unité de quantité est requise"]),
                ],
            ])
            ->add('date_semis_prod', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'La date de semis est requise']),
                ],
            ])
            ->add('date_recolte_prod', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'La date de récolte est requise']),
                ],
            ])
            ->add('cree_le_prod', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'La date de création est requise']),
                ],
            ])
            ->add('mis_a_jour_le_prod', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'La date de mise à jour est requise']),
                ],
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom_categorie',
                'constraints' => [
                    new NotBlank(['message' => 'La catégorie est requise']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
