#Screen

Web site screenshot tool based on PHP and [PhantomJS](http://phantomjs.org/ "")
You can use it to take screenshots for testing or monitoring service

## Install

Via Composer

``` bash
$ composer require microweber/screen
```

If on any unix system, you need to make the `bin` executable `chmod +x /path/to/screen/bin/phantomjs`

The directory `/path/to/screen/jobs` must be writeble as well.

##Linux requirements

 * FontConfig -  `apt-get/yum install fontconfig`
 * FreeType - `apt-get/yum install freetype*`

##Usage

With this library you can make use of PhantomJs to screenshot a website.

Check our [demo](/demo) or read the following instructions.

Creating the object, you can either pass the url on the constructer or set it later on
``` php
use Screen\Capture;

$url = 'https://github.com';

$screenCapture = new Capture($url);
// or
$screenCapture = new Capture();
$screenCapture->setUrl($url);
```

You can also set the browser dimensions
``` php
$screenCapture->setWidth(1200);
$screenCapture->setHeight(800);
```
This will output all the page including the content rendered beyond the setted dimensions (e.g.: all the scrollable content), if you want just the content inside those boudaries you need to clip the result
``` php
// You also need to set the width and height.
$screenCapture->setClipWidth(1200);
$screenCapture->setClipHeight(800);
```

Some webpages don't have a background color setted to the body, if you want you can set the color using this method
``` php
$screenCapture->setBackgroundColor('#ffffff');
```

You can also set the User Agent
``` php
$screenCapture->setUserAgentString('Some User Agent String');
```

And most importantly, save the result
``` php
$fileLocation = '/some/dir/test.jpg';
$screen->save($fileLocation);
```

##Other configurations
Additionally to the basic usage, you can set so extra configurations.

You can change the where the PhantomJS binary file is.
``` php
$screenCapture->binPath = '/path/to/bin/dir/';
// This will result in /path/to/bin/dir/phantomjs
```

Change the jobs location
``` php
$screenCapture->jobs->setLocation('/path/to/jobs/dir/');
echo $screenCapture->jobs->getLocation(); // -> /path/to/jobs/dir/
```

And set an output base location
``` php
$screenCapture->output->setLocation('/path/to/output/dir/');
echo $screenCapture->output->getLocation(); // -> /path/to/output/dir/

// if the output location is setted
$screenCapture->save('file.jpg');
// will save the file to /path/to/output/dir/file.jpg
```

You can also clean/delete all the generated job files like this:
``` php
$screenCapture->jobs->clean();
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- [Peter Ivanov](https://github.com/peter-mw)
- [André Filipe](https://github.com/MASNathan)
- [All Contributors](../../contributors)

Thanks to the [PhantomJS](http://phantomjs.org/ "Headless browser") ([LICENSE](https://github.com/ariya/phantomjs/blob/master/LICENSE.BSD)) guys for creating their awesome WebKit scripting interface.

This tool was originally created to take screenshots for [Microweber](http://microweber.com/ "Open Source CMS")
