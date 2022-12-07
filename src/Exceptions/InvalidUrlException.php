<?php

namespace Screen\Exceptions;

use Exception;

class InvalidUrlException extends ScreenException
{
    public function __construct(string $url, int $code = 0, Exception $previous = null)
    {
        $message = sprintf("The url '%s' is not valid.", $url);

        parent::__construct($message, $code, $previous);
    }
}
