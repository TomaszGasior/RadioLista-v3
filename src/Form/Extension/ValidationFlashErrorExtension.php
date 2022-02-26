<?php

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;

class ValidationFlashErrorExtension extends AbstractTypeExtension
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event){
            $form = $event->getForm();

            if ($form->isRoot() && false === $form->isValid()) {
                /** @var Session */
                $session = $this->requestStack->getCurrentRequest()->getSession();

                $session->getFlashBag()->add('error', 'common.notification.invalid_form');
            }
        });
    }

    static public function getExtendedTypes(): array
    {
        return [FormType::class];
    }
}
