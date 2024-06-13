<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
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
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Conditions d\'utilisations',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions d\'utilisations.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password',
                'novalidate' => 'novalidate'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    // Mot de passe d'au moins 8 caractères, incluant chiffres et lettres.
                    new Regex([
                        'pattern' => '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/',
                        'message' => 'Votre mot de passe doit contenir au moins 8 caractères, incluant chiffres et lettres.',
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Confirmer le mot de passe',
                'attr' => ['autocomplete' => 'new-password',
                'novalidate' => 'novalidate'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez confirmer votre mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    // Mot de passe d'au moins 8 caractères, incluant chiffres et lettres.
                    new Regex([
                        'pattern' => '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/',
                        'message' => 'Votre mot de passe doit contenir au moins 8 caractères, incluant chiffres et lettres.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
