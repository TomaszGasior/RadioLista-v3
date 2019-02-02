<?php

namespace App\Controller;

use App\Entity\RadioTable;
use Doctrine\DBAL\Exception\ConnectionException;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class DarkErrorController extends ExceptionController
{
    public function show(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null): Response
    {
        if ($this->debug) {
            return parent::showAction($request, $exception, $logger);
        }

        $message = null;

        if (is_a($exception->getClass(), ConnectionException::class, true)) {
            $message = 'DatabaseConnection';
        }
        elseif (is_a($exception->getClass(), HttpException::class, true)) {
            if (403 === $exception->getStatusCode() || 400 === $exception->getStatusCode()) {
                $message = 'UnexpectedRequest';
            }
            elseif (404 === $exception->getStatusCode()) {
                $message = 'NotFound';

                if (false !== strpos($exception->getMessage(), RadioTable::class)
                    || false !== strpos($exception->getMessage(), 'RADIOTABLE_SHOW')) {
                    $message = 'RadioTableNotFound';
                }
            }
        }

        return new Response(
            $this->twig->render('dark-error.html.twig', ['message' => $message])
        );
    }
}
