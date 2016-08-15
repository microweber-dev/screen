<?php

// Use the first autoload instead if you don't want to install composer
//require_once '../autoload.php';
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

if (isset($_GET['format'])) { // Format
    $screen->setImageType($_GET['format']);
}

$screen->includeJs(new \Screen\Injection\Scripts\FacebookHideCookiesPolicy());
$screen->includeJs(new \Screen\Injection\Scripts\FacebookHideSignUp());
$screen->includeJs(new \Screen\Injection\LocalPath('path/to/my/script.js'));
$screen->includeJs(new \Screen\Injection\Url('https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js'));
$screen->includeJs("allert('asd');");

$fileLocation = 'test';
$screen->save($fileLocation);

header('Content-Type:' . $screen->getImageType()->getMimeType());
header('Content-Length: ' . filesize($screen->getImageLocation()));
readfile($screen->getImageLocation());
