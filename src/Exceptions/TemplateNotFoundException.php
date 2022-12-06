<?php

namespace Screen\Exceptions;

use Exception;

class TemplateNotFoundException extends ScreenException
{
    public function __construct(string $templateName, int $code = 0, Exception $previous = null)
    {
        $message = sprintf("The template '%s' does not exist!", $templateName);

        parent::__construct($message, $code, $previous);
    }
}
