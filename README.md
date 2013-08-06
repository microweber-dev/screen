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

Demo
====
Main file http://screen.microweber.com/

Screenshot from api http://screen.microweber.com/shot.php?url=http%3A%2F%2Fwww.reddit.com%2Fr%2Fphp



Dependencies
=====
 * FontConfig must be installed -  `apt-get/yum install fontconfig`
 * FreeType is also required - `apt-get/yum install freetype*`



 