<?php

namespace Screen\Injection\Scripts;

use Screen\Injection\LocalPath;

class FacebookHideTopBar extends LocalPath
{
    public function __construct()
    {
        $path = __DIR__ . '/../../../scripts/facebook-hide-top-bar.js';

        parent::__construct($path);
    }
}
