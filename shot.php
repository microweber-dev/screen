<?php

require_once 'vendor/autoload.php';

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

if (isset($_REQUEST['download'])) {
    $download = $_REQUEST['download'];
}

$fileLocation = 'test.jpg';
$screen->save($fileLocation);

if (isset($_REQUEST['download']) && $_REQUEST['download']) {
    $parsedUrl = parse_url($_REQUEST['url']);
    $fileName = 'ScreeCapture-' . $parsedUrl['host'] . '.jpg';

    $type = 'image/jpeg';
    header("Content-disposition: attachment; filename={$fileName}");
    header("Content-type: {$type}");
    readfile($fileLocation);
} else {
    $type = 'image/jpeg';
    header('Content-Type:' . $type);
    header('Content-Length: ' . filesize($fileLocation));
    readfile($fileLocation);
}
