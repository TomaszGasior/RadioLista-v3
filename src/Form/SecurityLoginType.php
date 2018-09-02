<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecurityLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nazwa użytkownika',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Hasło',
            ])
            ->add('rememberMe', CheckboxType::class, [
                'label'    => 'Zapamiętaj mnie',
                'required' => false,
            ])
        ;
    }
}
