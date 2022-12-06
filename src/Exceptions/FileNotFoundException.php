<?php

namespace Screen\Exceptions;

use Exception;

class FileNotFoundException extends ScreenException
{
    public function __construct(string $path, int $code = 0, Exception $previous = null)
    {
        $message = sprintf("The file was not found at '%s'.", $path);

        parent::__construct($message, $code, $previous);
    }
}
