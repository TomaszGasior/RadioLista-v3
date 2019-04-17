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
    public function show(Request $request, FlattenException $exception,
                         DebugLoggerInterface $logger = null): Response
    {
        if ($this->debug) {
            return parent::showAction($request, $exception, $logger);
        }

        $class = $exception->getClass();
        $message = null;

        if (is_a($class, ConnectionException::class, true)) {
            $message = 'DatabaseConnection';
        }
        elseif (is_a($class, HttpException::class, true)) {
            $statusCode = $exception->getStatusCode();

            if (403 === $statusCode || 400 === $statusCode) {
                $message = 'UnexpectedRequest';
            }
            elseif (404 === $statusCode) {
                $message = 'NotFound';

                $text = $exception->getMessage();
                if (false !== strpos($text, RadioTable::class) ||
                    false !== strpos($text, 'RADIOTABLE_SHOW')) {
                    $message = 'RadioTableNotFound';
                }
            }
        }

        return new Response(
            $this->twig->render('dark-error.html.twig', ['message' => $message])
        );
    }
}
