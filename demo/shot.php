<?php

require_once '../vendor/autoload.php';

if (!isset($_GET['url'])) {
    exit;
}

$screen = new Screen\Capture($_GET['url']);

if (isset($_GET['w'])) { // Width
    $screen->setWidth(intval($_GET['w']));
}

if (isset($_GET['h'])) { // Height
    $screen->setHeight(intval($_GET['h']));
}

if (isset($_GET['clipw'])) { // Clip Width
    $screen->setClipWidth(intval($_GET['clipw']));
}

if (isset($_GET['cliph'])) { // Clip Height
    $screen->setClipHeight(intval($_GET['cliph']));
}

if (isset($_GET['user-agent'])) { // User Agent String
    $screen->setUserAgentString($_GET['user-agent']);
}

if (isset($_GET['bg-color'])) { // Background Color
    $screen->setBackgroundColor($_GET['bg-color']);
}

$fileLocation = 'test.jpg';
$screen->save($fileLocation);

$type = 'image/jpeg';
header('Content-Type:' . $type);
header('Content-Length: ' . filesize($fileLocation));
readfile($fileLocation);
