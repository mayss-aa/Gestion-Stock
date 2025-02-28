<?php

namespace App\Form;

use App\Entity\Plante;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Positive;



class AddPlanteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_plante', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le nom de la plante ne peut pas être vide.']),
                    new Regex([
                        'pattern' => "/^[a-zA-ZÀ-ÿ\s]+$/",
                        'message' => "Le nom de la plante doit contenir uniquement des lettres."
                    ])
                ],
                'error_bubbling' => false
            ])
            ->add('date_plantation', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner une date de plantation.']),
                ],
                'error_bubbling' => false
            ])
            ->add('rendement_estime', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez renseigner un rendement estimé.']),
                    new Positive(['message' => 'Le rendement estimé doit être un nombre positif.'])
                ],
                'invalid_message' => 'Le rendement estimé doit être un nombre valide.',
                'error_bubbling' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plante::class,
        ]);
    }
}
