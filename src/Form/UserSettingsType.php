<?php

namespace App\Form;

use App\Entity\User;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class UserSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('aboutMe', CKEditorType::class, [
                'required' => false,
                'sanitize_html' => true,
            ])
            ->add('publicProfile')
            ->add('currentPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new SecurityAssert\UserPassword([
                        'groups' => 'ChangingPasswordTab',
                        'message' => 'user.incorrect_password',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'label_format' => 'user.settings.form.plainPassword.%name%',

                'invalid_message' => 'user.passwords_dont_match',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'label_format' => 'user.settings.form.%name%',
            'validation_groups' => function(FormInterface $form){
                if ($form->get('currentPassword')->getData() || $form->get('plainPassword')->getData()) {
                    return ['ChangingPasswordTab', 'RedefinePassword'];
                }

                return 'Default';
            },
        ]);
    }
}
