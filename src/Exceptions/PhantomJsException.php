<?php

namespace Screen\Exceptions;

class PhantomJsException extends \Exception
{
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        $message = is_array($message) ? implode(PHP_EOL, $message) : $message;

        parent::__construct($message, $code, $previous);
    }
}
