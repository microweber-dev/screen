<?php

namespace Screen\Injection\Scripts;

use Screen\Injection\LocalPath;

class FacebookHideLogin extends LocalPath
{
    public function __construct()
    {
        $path = __DIR__ . '/../../../scripts/facebook-hide-login.js';

        parent::__construct($path);
    }
}
