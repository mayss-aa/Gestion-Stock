<?php

namespace App\Form;

use App\Entity\Zone;
use App\Entity\Plante;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AddZoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_zone', TextType::class, [
                'label' => 'Nom de la Zone',
            ])
            ->add('superficie', NumberType::class, [
                'label' => 'Superficie (en hectares)',
            ])
            ->add('plante', EntityType::class, [
                'class' => Plante::class,
                'choice_label' => 'nom_plante',
                'label' => 'Plante affectée',
                'placeholder' => 'Sélectionner une plante',
            ]);
    }
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Zone::class,
        ]);
    }
}
