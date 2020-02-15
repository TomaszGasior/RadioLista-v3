<?php

namespace App\Controller;

use App\Entity\RadioTable;
use Doctrine\DBAL\Exception\ConnectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ErrorController;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DarkErrorController extends AbstractController
{
    private $errorController;
    private $debug;

    public function __construct(ErrorController $errorController, bool $debug)
    {
        $this->errorController = $errorController;
        $this->debug = $debug;
    }

    public function showError(\Throwable $exception, Request $request): Response
    {
        if ($request->attributes->get('showException', $this->debug)) {
            return ($this->errorController)($exception);
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
