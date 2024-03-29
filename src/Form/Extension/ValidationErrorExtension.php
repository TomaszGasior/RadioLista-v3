<?php

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This extension applies user experience tweaks when validation error occurs.
 *
 * * `autofocus` HTML attribute is added to all invalid form fields. Only one
 *   form field can be autofocused by browser but adding the attribute to all
 *   of them autofocuses first one from top of the page (order of form children
 *   in PHP does not have to match order of fields in HTML markup).
 *
 * * Generic notification about "invalid form below" is shown to the user.
 *   Here it's needed to make sure it's only one to show it only once.
 */
class ValidationErrorExtension extends AbstractTypeExtension
{
    public function __construct(private RequestStack $requestStack) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use ($options) {
            $form = $event->getForm();

            if (!$form->isRoot() || !$options['validation_error_generic_notification']) {
                return;
            }

            if (!$form->isValid()) {
                $session = $this->requestStack->getCurrentRequest()->getSession();

                if ($session instanceof FlashBagAwareSessionInterface) {
                    $session->getFlashBag()->add('error', 'common.notification.invalid_form');
                }
            }
        });
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        if ($form->isSubmitted() && !$form->isValid()) {
            $view->vars['attr']['autofocus'] = true;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('validation_error_generic_notification', true);
    }

    static public function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
