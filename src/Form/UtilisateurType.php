<?php
namespace App\Form;

use App\Entity\Utilisateur;
use App\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('numTel', TextType::class, [
                'label' => 'Numéro de Téléphone',
                'required' => false,
                'attr' => ['placeholder' => 'Numéro de Téléphone'],
            ])
            ->add('genre', ChoiceType::class, [
                'label' => 'Genre',
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                ],
                'placeholder' => 'Choisir un genre',
            ])
            ->add('date_naissance', DateType::class, [
                'label' => 'Date de Naissance',
                'widget' => 'single_text',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de Passe',
                'required' => false,
                'mapped' => false, // This field is not directly mapped to the entity
            ])
            
            
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'nomRole',
                'label' => 'Rôle',
                'placeholder' => 'Choisir un rôle',
                'required' => true,
            ])
            ->add('photoFile', FileType::class, [
                'label' => 'Photo',
                'required' => false,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
