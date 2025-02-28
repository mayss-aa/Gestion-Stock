<?php

namespace App\Form;

use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('email', TextType::class, [
                'label' => 'Email'
            ])
            ->add('numTel', TextType::class, [ // Added num_tel field
                'label' => 'Numéro de Téléphone',
                'required' => false, // Set to true if you want it to be required
                'attr' => ['placeholder' => 'Numéro de Téléphone'] // Optional placeholder
            ])
            ->add('genre', ChoiceType::class, [
                'label' => 'Genre',
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme'
                ],
                'placeholder' => 'Choisir un genre'
            ])
            ->add('date_naissance', DateType::class, [
                'label' => 'Date de Naissance',
                'widget' => 'single_text',
            ])
        
            // src/Form/RegistrationFormType.php
            ->add('password', PasswordType::class)

           
            /*->add('password', PasswordType::class, [
                'label' => 'Password',
                 // It will not be saved directly in the database, but we'll hash it manually.
                
            ])*/
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'nomRole',
                'label' => 'Rôle',
                'placeholder' => 'Choisir un rôle',
                'required' => true,
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}

