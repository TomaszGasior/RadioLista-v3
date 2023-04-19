<?php

namespace App\Form;

use App\Entity\RadioTable;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RadioTableCreateType extends AbstractType
{
    public function getParent(): string
    {
        return RadioTableSettingsType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setData(new RadioTable('', $options['owner']));

        foreach ($builder->all() as $child) {
            if (in_array($child->getName(), ['name', 'status'])) {
                continue;
            }

            $builder->remove($child->getName());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('owner')
            ->setAllowedTypes('owner', User::class)
        ;
    }
}
