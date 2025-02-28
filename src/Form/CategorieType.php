<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Regex;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_categorie', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Le nom de la catégorie est requis']),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom de la catégorie ne peut pas dépasser {{ limit }} caractères'
                    ]),
                ],
            ])
            ->add('description_categorie', null, [
                'constraints' => [
                    new NotBlank(['message' => 'La description de la catégorie est requis']),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères'
                    ]),
                ],
            ])
            ->add('saisonDeRecolte', null, [
                'constraints' => [
                    new NotBlank(['message' => 'La saison  la catégorie est requis']),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'La saison de récolte ne peut pas dépasser {{ limit }} caractères'
                    ]),
                    new Choice([
                        'choices' => ['printemps', 'été', 'automne', 'hiver'],
                        'message' => 'La saison de récolte doit être parmi les valeurs suivantes: printemps, été, automne, hiver'
                    ]),
                ],
            ])
            ->add('temperatureIdeale', null, [
                'constraints' => [
                    new NotBlank(['message' => 'La temperature de la catégorie est requis']),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'La température idéale ne peut pas dépasser {{ limit }} caractères'
                    ]),
                    new Regex([
                        'pattern' => '/^\d+-\d+°C$/',
                        'message' => 'La température doit être au format "XX-XX°C"'
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
