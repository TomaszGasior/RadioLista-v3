<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, [
                'label' => 'Temat wiadomości',
            ])
            ->add('email', EmailType::class, [
                'label'    => 'Twój adres e-mail',
                'required' => false,
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Treść wiadomości',
            ])
        ;
    }
}
