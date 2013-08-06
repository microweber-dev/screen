Screen
======

Web site screenshot tool based on PHP and PhanotomJS

You can use it to take screenshots for resting or monitoring service

Usage
======

* Upload to your webserver 
* Make the `bin` executable `chmod +x /var/www/html/screen/bin/phantomjs`
* Make your folder writable
* Open your browser to index.php


API
=====

You can directly render the taken screen-shot with the `shot.php` file

You can render any link as image by passig it as url parameter

`shot.php?url=http%3A%2F%2Fwww.reddit.com%2Fr%2Fphp`



 