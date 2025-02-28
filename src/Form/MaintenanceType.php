<?php
// src/Form/MaintenanceType.php

namespace App\Form;

use App\Entity\Maintenance;
use App\Entity\Machine;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class MaintenanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('machine', EntityType::class, [
                'class' => Machine::class,
                'choice_label' => 'nom_machine',
            ])
            ->add('date_maintenance', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'min' => $options['last_date'] ? $options['last_date']->format('Y-m-d') : (new \DateTime())->format('Y-m-d')
                ],
            ])
            ->add('description', TextType::class)
            
            ->add('cout_maintenance', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0.01, // Empêche les valeurs négatives dans l'UI
                    'step' => '0.01'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Maintenance::class,
            'last_date' => null, // Permet de passer la dernière date de maintenance
        ]);
    }
}
