<?php

declare(strict_types=1);

namespace App\Infrastructure\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Exception;

class ClientException extends Exception
{
    /**
    * @param string $message
    * @param int $code
    * @param \Throwable|null $previous
    */
    public function __construct($message = "Requisição do cliente inválida.", $code = Response::HTTP_BAD_REQUEST, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
