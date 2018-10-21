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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event){
            $form = $event->getForm();

            if ($form->isRoot()) {
                $session = $this->requestStack->getCurrentRequest()->getSession();

                if (!$form->isValid()) {
                    $session->getFlashBag()->add('error', $form->getErrors(true));
                }
            }
        });
    }

    public function getExtendedType()
    {
        return FormType::class;
    }
}
