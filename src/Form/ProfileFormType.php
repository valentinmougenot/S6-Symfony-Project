<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('lastName', null, [
            'label' => 'Nom',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un nom',
                ]),
                new Length([
                    'max' => 255,
                    'maxMessage' => 'Votre nom ne peut pas contenir plus de {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('firstName', null, [
            'label' => 'Prénom',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un prénom',
                ]),
                new Length([
                    'max' => 255,
                    'maxMessage' => 'Votre prénom ne peut pas contenir plus de {{ limit }} caractères',
                ]),
            ],
        ])
        ->add('email', EmailType::class, [
            'help' => 'Votre email ne sera jamais partagé avec des tiers.',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un email',
                ]),
                new Length([
                    'max' => 255,
                    'maxMessage' => 'Votre email ne peut pas contenir plus de {{ limit }} caractères',
                ]),
                new Regex([
                    // pattern pour un email de la forme xxx@yyy.zz
                    'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/',
                    'message' => 'Veuillez entrer un email valide',
                ]),
                // Regarder que l'email n'existe pas déjà dans la base de données
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => User::class,
        ]);
    }
}
