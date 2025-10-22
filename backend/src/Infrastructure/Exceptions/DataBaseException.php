<?php

declare(strict_types=1);

namespace App\Infrastructure\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Exception;

class DataBaseException extends Exception
{
    /**
    * @param string $message
    * @param int $code
    * @param \Throwable|null $previous
    */
    public function __construct(
        $message = "Failed to connect to the bank",
        $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        \Throwable $previous = null
        )
    {
        parent::__construct($message, $code, $previous);
    }
}
