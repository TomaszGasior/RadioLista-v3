<?php

namespace App\Controller;

use App\Entity\RadioTable;
use Doctrine\DBAL\Exception\ConnectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ErrorController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Translation\Translator;
use Throwable;

class DarkErrorController extends AbstractController
{
    public function __construct(
        public ErrorController $errorController,
        public Translator $translator,
        public RequestStack $requestStack,
        public bool $debug,
        public array $locales,
    ) {}

    public function showError(Throwable $exception, Request $request): Response
    {
        if ($request->attributes->get('showException', $this->debug)) {
            return ($this->errorController)($exception);
        }

        // Site language is defined by locale assigned to route. Set preferred
        // language for better user experience when route is unknown (real 404).
        if (!$this->requestStack->getMainRequest()->attributes->get('_route')) {
            $this->translator->setLocale($request->getPreferredLanguage($this->locales));
        }

        $message = null;

        if ($exception instanceof ConnectionException) {
            $message = 'DatabaseConnection';
        }
        elseif ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();

            if (403 === $statusCode || 400 === $statusCode) {
                $message = 'UnexpectedRequest';
            }
            elseif (404 === $statusCode) {
                $message = 'NotFound';

                $text = $exception->getMessage();
                if (false !== strpos($text, RadioTable::class) ||
                    false !== strpos($text, 'RADIO_TABLE_SHOW')) {
                    $message = 'RadioTableNotFound';
                }
            }
        }

        return $this->render('dark-error.html.twig', ['message' => $message]);
    }
}
