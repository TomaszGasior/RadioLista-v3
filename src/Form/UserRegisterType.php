<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'help' => 'user.register.form.name.help',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'label_format' => 'user.register.form.plainPassword.%name%',
                'type' => PasswordType::class,
                'options' => [
                    'hash_property_path' => 'password',
                ],
                'constraints' => [
                    new Assert\NotBlank,
                ],
                'invalid_message' => 'user.passwords_dont_match',
            ])
            ->add('acceptServiceTerms', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'user.service_terms_required',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'label_format' => 'user.register.form.%name%',
        ]);
    }
}
