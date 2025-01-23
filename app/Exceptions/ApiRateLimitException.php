<?php

namespace App\Exceptions;

use Exception;

class ApiRateLimitException extends Exception
{
    public function __construct(
        public readonly int $retryAfter = 60,
        string $message = 'API rate limit exceeded'
    ) {
        parent::__construct($message, 429);
    }
}
