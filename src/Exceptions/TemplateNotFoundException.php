<?php

namespace Screen\Exceptions;

class TemplateNotFoundException extends ScreenException
{
    public function __construct($templateName, $code = 0, Exception $previous = null)
    {
        $message = sprintf("The template '%s' does not exist!", $templateName);

        parent::__construct($message, $code, $previous);
    }
}
