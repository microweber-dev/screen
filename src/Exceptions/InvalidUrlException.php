<?php

namespace Screen\Exceptions;

class InvalidUrlException extends ScreenException
{
    public function __construct($url, $code = 0, Exception $previous = null)
    {
        $message = sprintf("The url '%s' is not valid.", $url);

        parent::__construct($message, $code, $previous);
    }
}
