<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre',
            ])
            ->add('description', null, [
                'label' => 'Description',
            ])
            ->add('date', null, [
                'widget' => 'single_text',
                'label' => 'Date',
            ])
            ->add('max_size', null, [
                'label' => 'Nombre de places',
            ])
            ->add('public', null, [
                'label' => 'Public',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
