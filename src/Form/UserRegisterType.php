<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nazwa użytkownika',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,

                'first_options' => [
                    'label' => 'Hasło',
                ],
                'second_options' => [
                    'label' => 'Hasło ponownie',
                ],
            ])
            ->add('acceptServiceTerms', CheckboxType::class, [
                'label'       => 'Akceptuję regulamin serwisu RadioLista',
                'mapped'      => false,
                'required'    => false,
                'constraints' => [
                    new Assert\IsTrue([
                        'groups'  => 'Registration',
                        'message' => 'Akceptacja regulaminu serwisu jest wymagana.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'        => User::class,
            'validation_groups' => ['Registration'],
        ]);
    }
}
