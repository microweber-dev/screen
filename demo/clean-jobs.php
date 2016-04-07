<?php

require_once '../vendor/autoload.php';

// If you already have a Capture instance created you can
$screen = new Screen\Capture('url');
$screen->jobs->clean();

// if not you can simply create a jobs instance
$jobs = new \Screen\Jobs();
$jobs->clean();
