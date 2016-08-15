<?php

namespace Screen\Exceptions;

class FileNotFoundException extends ScreenException
{
    public function __construct($path, $code = 0, Exception $previous = null)
    {
        $message = sprintf("The file was not found at '%s'.", $path);

        parent::__construct($message, $code, $previous);
    }
}
