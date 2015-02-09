Screen
======

Web site screenshot tool based on PHP and [PhanotomJS](http://phantomjs.org/ "")

You can use it to take screenshots for testing or monitoring service


Usage
======

* Upload to your webserver 
* Make the `bin` executable `chmod +x /var/www/html/screen/bin/phantomjs`
* Make your folder writable
* Open your browser to index.php


API
=====

You can directly render the taken screen-shot with the `shot.php` file

You can render any link by passing it as `url` parameter

`shot.php?url=google.com`

You can specify height and width:
`shot.php?url=google.com&w=300&h=100`

If you want to crop/clip the screen shot, you can do so like this:
`shot.php?url=google.com&w=800&h=600&clipw=800&cliph=600`

To download the image, just go to `shot.php?url=google.com&download=true`

 
Dependencies
=====
 * FontConfig must be installed -  `apt-get/yum install fontconfig`
 * FreeType is also required - `apt-get/yum install freetype*`


Thanks
====
Thanks to the [PhanotomJS](http://phantomjs.org/ "Headless browser") guys for creating their awesome WebKit scripting interface.

This tool was originally created to take screenshots for [Microweber](http://microweber.com/ "open source cms")
