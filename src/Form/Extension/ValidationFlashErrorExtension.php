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

                $message = $form->getErrors(true)[0]->getMessage();

                // Set generic error message if current text isn't specified by this application.
                if (false !== stripos($message, 'this') || false !== stripos($message, 'value')) {
                    $message = 'Formularz został wypełniony niepoprawnie.';
                }

                $session->getFlashBag()->add('error', $message);
            }
        });
    }

    static public function getExtendedTypes(): array
    {
        return [FormType::class];
    }
}
