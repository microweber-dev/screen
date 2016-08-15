<?php

namespace Screen\Injection\Scripts;

use Screen\Injection\LocalPath;

class FacebookHideSignUp extends LocalPath
{
    public function __construct()
    {
        $path = __DIR__ . '/../../../scripts/facebook-hide-signup.js';

        parent::__construct($path);
    }
}
