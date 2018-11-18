<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class UserSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('aboutMe', TextareaType::class, [
                'label'    => 'Kilka słów o mnie',
                'required' => false,
            ])
            ->add('publicProfile', null, [
                'label' => 'Włącz profil publiczny i stronę profilową',
            ])
            ->add('currentPassword', PasswordType::class, [
                'label'       => 'Obecne hasło',
                'mapped'      => false,
                // 'required' => false,
                'constraints' => [
                    new SecurityAssert\UserPassword,
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'property_path' => 'password',

                'type' => PasswordType::class,
                // 'required' => false,

                'first_options' => [
                    'label' => 'Nowe hasło',
                ],
                'second_options' => [
                    'label' => 'Nowe hasło ponownie',
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