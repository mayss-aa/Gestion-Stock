<?php
// src/Form/MachineType.php

namespace App\Form;

use App\Entity\Machine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class MachineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom_machine', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nom de la machine'],
            ])
            ->add('etat_machine', ChoiceType::class, [
                'choices' => [
                    'Nouvelle' => 'nouvelle',
                    'Obsolète' => 'obsolete',
                    'À réparer' => 'a_reparer',
                ],
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Choisissez un état', // Option facultative
                'required' => true,
            ])
            ->add('brand_machine', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Marque de la machine'],
            ])
            ->add('date_achat', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Date d\'achat'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Machine::class,
        ]);
    }
}