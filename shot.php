<?php
$cache_life = 30; //caching time, in seconds

if (!isset($_REQUEST['url'])) {
    exit(1);
}
$url = $_REQUEST['url'];

$url = trim(urldecode($url));
if ($url == '') {
    exit(2);
}

if (!stristr($url, 'http://') and !stristr($url, 'https://')) {
    $url = 'http://' . $url;

}

$url_segs = parse_url($url);
if (!isset($url_segs['host'])) {
    exit(3);
}


$here = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$bin_files = $here . 'bin' . DIRECTORY_SEPARATOR;
$jobs = $here . 'jobs' . DIRECTORY_SEPARATOR;
$cache = $here . 'cache' . DIRECTORY_SEPARATOR;
if (!is_dir($jobs)) {
    mkdir($jobs);
}
if (!is_dir($cache)) {
    mkdir($cache);
}





$screen_file = $url_segs['host'] . crc32($url) . '.jpg';
$cache_job = $cache . $screen_file;


$refresh = false;
if (is_file($cache_job)) {
$filemtime = @filemtime($cache_job);  // returns FALSE if file does not exist
if (!$filemtime or (time() - $filemtime >= $cache_life)){
$refresh = true;
}
}






if (!is_file($cache_job) or $refresh == true) {
    $src = "

var page = require('webpage').create();
page.open('{$url}', function () {
    page.render('{$screen_file}');
    phantom.exit();
});


";
    $job_file = $jobs . $url_segs['host'] . crc32($src) . '.js';
    file_put_contents($job_file, $src);

    $exec = $bin_files . 'phantomjs ' . $job_file;


    //var_dump($here.$screen_file);
    exec($exec);

    if (is_file($here . $screen_file)) {
        rename($here . $screen_file, $cache_job);
    }
}



if (is_file($cache_job)) {

    $file = $cache_job;
    $type = 'image/jpeg';
    header('Content-Type:' . $type);
    header('Content-Length: ' . filesize($file));
    readfile($file);

}







 var_dump($exec);

?>
