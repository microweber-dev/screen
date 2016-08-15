<?php

namespace Screen\Injection\Scripts;

use Screen\Injection\LocalPath;

class FacebookHideCookiesPolicy extends LocalPath
{
    public function __construct()
    {
        $path = __DIR__ . '/../../../scripts/facebook-hide-cookies-policy.js';

        parent::__construct($path);
    }
}
