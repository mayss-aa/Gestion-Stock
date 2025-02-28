<?php

namespace App\Form;

use App\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_role', TextType::class, [ // Keep 'nom_role' as the field name
                'label' => 'Nom du rôle',
            ])
            ->add('description', TextType::class, [ // Specify the type explicitly
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Description du rôle'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Role::class,
        ]);
    }
}