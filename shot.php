<?php
$cache_life = 60; //caching time, in seconds
$download = false;
if (!isset($_REQUEST['url'])) {
    exit();
}
$url = $_REQUEST['url'];

$url = trim(urldecode($url));
if ($url == '') {
    exit();
}

if (!stristr($url, 'http://') and !stristr($url, 'https://')) {
   $url = 'http://' . $url;

}

$url_segs = parse_url($url);
if (!isset($url_segs['host'])) {
    exit();
}


$here = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$bin_files = $here . 'bin' . DIRECTORY_SEPARATOR;
$jobs = $here . 'jobs' . DIRECTORY_SEPARATOR;
$cache = $here . 'cache' . DIRECTORY_SEPARATOR;

if (!is_dir($jobs)) {
    mkdir($jobs);
    file_put_contents($jobs . 'index.php', '<?php exit(); ?>');

}
if (!is_dir($cache)) {
    mkdir($cache);
    file_put_contents($cache . 'index.php', '<?php exit(); ?>');

}


$w = 1024;
$h = 768;
$t = '';
$l = '';
$b = '#fff';
$f = 'image/jpeg';

if (isset($_REQUEST['w'])) {
    $w = intval($_REQUEST['w']);
}

if (isset($_REQUEST['h'])) {
    $h = intval($_REQUEST['h']);
}

if (isset($_REQUEST['clipw'])) {
    $clipw = intval($_REQUEST['clipw']);
}

if (isset($_REQUEST['cliph'])) {
    $cliph = intval($_REQUEST['cliph']);
}

if (isset($_REQUEST['top'])) {
    $t = intval($_REQUEST['top']);
}

if (isset($_REQUEST['left'])) {
    $l = intval($_REQUEST['left']);
}

if (isset($_REQUEST['bg'])) {
    $b = '#' . $_REQUEST['bg'];
}

if (isset($_REQUEST['format'])) {
    switch ($_REQUEST['format']) {
        case 'jpg':
            $f = 'image/jpeg';
            break;

        case 'png':
            $f = 'image/png';
            break;

        default:
            $f = 'image/jpeg';
            break;
    }
}

if (isset($_REQUEST['download'])) {
    $download = $_REQUEST['download'];
}

$url = strip_tags($url);
$url = str_replace(';', '', $url);
$url = str_replace('"', '', $url);
$url = str_replace('\'', '/', $url);
$url = str_replace('<?', '', $url);
$url = str_replace('<?', '', $url);
$url = str_replace('\077', ' ', $url);


$screen_file = $url_segs['host'] . crc32($url) . '_' . $w . '_' . $h . ($f == 'image/png' ? '.png' : '.jpg');
$cache_job = $cache . $screen_file;


$refresh = false;
if (is_file($cache_job)) {
    $filemtime = @filemtime($cache_job); // returns FALSE if file does not exist
    if (!$filemtime or (time() - $filemtime >= $cache_life)) {
        $refresh = true;
    }
}


$url = escapeshellcmd($url);

if (!is_file($cache_job) or $refresh == true) {
    $src = "

    var page = require('webpage').create();

    page.viewportSize = { width: {$w}, height: {$h} };

    ";

    if (isset($clipw) && isset($cliph)) {
     if($t !== '') {
          $src .= "page.clipRect = { top: {$t}, width: {$clipw}, height: {$cliph} };";
     } elseif($l !== '') {
          $src .= "page.clipRect = { left: {$l}, width: {$clipw}, height: {$cliph} };";
     } elseif($t !== '' && $l !== '') {
          $src .= "page.clipRect = { top: {$t}, left: {$l}, width: {$clipw}, height: {$cliph} };";
     } else {
          $src .= "page.clipRect = { width: {$clipw}, height: {$cliph} };";
     }
    }

    if($f == 'image/jpeg') {
        $src .= "

        page.open('{$url}', function () {
            page.evaluate(function(w, h, b) {
              $('body').css('width', w + 'px');
              $('body').css('height', h + 'px');
              $('body').css('backgroundColor', b);
            }, {$w}, {$h}, '{$b}');
            page.render('{$screen_file}');
            phantom.exit();
        });


        ";
    } else {
        $src .= "

        page.open('{$url}', function () {
            page.evaluate(function(w, h) {
              $('body').css('width', w + 'px');
              $('body').css('height', h + 'px');
            }, {$w}, {$h});
            page.render('{$screen_file}');
            phantom.exit();
        });


        ";
    }

    $job_file = $jobs . $url_segs['host'] . crc32($src) . '.js';
    file_put_contents($job_file, $src);

    $exec = $bin_files . 'phantomjs ' . $job_file;

    $escaped_command = escapeshellcmd($exec);

    exec($escaped_command);

    if (is_file($here . $screen_file)) {
        rename($here . $screen_file, $cache_job);
    }
}


if (is_file($cache_job)) {
    if ($download != false) {
        $file = $cache_job;
        $file_name=basename($file);
        header("Content-disposition: attachment; filename={$file_name}");
        header("Content-type: {$f}");
        readfile($file);
    } else {
        $file = $cache_job;
        header("Content-Type: {$f}");
        header("Content-Length: " . filesize($file));
        readfile($file);
    }
}