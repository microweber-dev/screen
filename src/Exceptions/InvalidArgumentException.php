<?php

namespace Screen\Exceptions;

use Exception;

class InvalidArgumentException extends ScreenException
{
    public function __construct(string $message, int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
