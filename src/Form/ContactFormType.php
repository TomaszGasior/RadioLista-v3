<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, [
                'label' => 'Temat wiadomości',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Nie wypełniono tematu wiadomości.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Twój adres e-mail',
                'required' => false,
                'constraints' => [
                    new Assert\Email([
                        'message' => 'Podany adres e-mail jest niewłaściwy.',
                    ]),
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Treść wiadomości',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Nie wypełniono treści wiadomości.',
                    ]),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'Nie wypełniono treści wiadomości.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'timed_spam' => true,
            'timed_spam_min' => 5,
            'timed_spam_max' => 7200,
            'timed_spam_message' => 'Ochrona przed spamem: zbyt szybko wysłana wiadomość.',
        ]);
    }
}
