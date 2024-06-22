<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EventFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => 'Titre',
            ])
            ->add('date_from', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date à partir de',
            ])
            ->add('date_to', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date jusqu\'à',
            ])
            ->add('is_public', ChoiceType::class, [
                'choices' => [
                    'Tous' => null,
                    'Oui' => true,
                    'Non' => false,
                ],
                'required' => false,
                'label' => 'Public',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
