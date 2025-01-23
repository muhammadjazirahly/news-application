<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ApiException extends Exception
{
    public function __construct(
        public readonly string $apiName,
        string $message,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct("[$apiName] $message", $code, $previous);
    }
}