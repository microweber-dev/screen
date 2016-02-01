<?php

require_once '../vendor/autoload.php';

if (!isset($_REQUEST['url'])) {
    exit;
}

$screen = new Screen\Capture($_REQUEST['url']);

if (isset($_REQUEST['w'])) { // Width
    $screen->setWidth(intval($_REQUEST['w']));
}

if (isset($_REQUEST['h'])) { // Height
    $screen->setHeight(intval($_REQUEST['h']));
}

if (isset($_REQUEST['clipw'])) { // Clip Width
    $screen->setClipWidth(intval($_REQUEST['clipw']));
}

if (isset($_REQUEST['cliph'])) { // Clip Height
    $screen->setClipHeight(intval($_REQUEST['cliph']));
}

$fileLocation = 'test.jpg';
$screen->save($fileLocation);

$type = 'image/jpeg';
header('Content-Type:' . $type);
header('Content-Length: ' . filesize($fileLocation));
readfile($fileLocation);

