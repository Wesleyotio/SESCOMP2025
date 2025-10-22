<?php

declare(strict_types=1);

namespace App\Application\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Exception;

class ProductException extends Exception
{
    /**
    * @param string $message
    * @param int $code
    * @param \Throwable|null $previous
    */
    public function __construct(string $message = "Requisição de produto inválida.", int $code = Response::HTTP_BAD_REQUEST, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
