<?php

namespace Screen;

use Screen\Exceptions\InvalidArgumentException;
use Screen\Exceptions\PhantomJsException;
use Screen\Exceptions\TemplateNotFoundException;
use Screen\Image\Types;
use Screen\Image\Types\Type;
use Screen\Injection\LocalPath;
use Screen\Injection\Url;
use Screen\Location\Jobs;
use Screen\Location\Output;

/**
 * Class Capture
 *
 * @package Screen
 * @author  André Filipe <andre.r.flip@gmail.com>
 * @license MIT https://github.com/microweber/screen/blob/master/LICENSE
 */
class Capture
{
    /**
     * URL to capture the screen of
     *
     * @var string
     */
    protected $url;

    /**
     * dom element top position
     * @var string
     */
    protected $top;
    
    /**
     * dom element left position
     * @var string
     */
    protected $left;
    
    /**
     * Width of the page to render
     *
     * @var int
     */
    protected $width = 1024;

    /**
     * Height of the page to render
     *
     * @var int
     */
    protected $height = 768;

    /**
     * Width of the page to clip
     *
     * @var int
     */
    protected $clipWidth;

    /**
     * Height of the page to clip
     *
     * @var int
     */
    protected $clipHeight;

    /**
     * Default body background color is white
     *
     * @var string
     */
    protected $backgroundColor = '';

    /**
     * Image Type, default is jpeg
     *
     * @var Type
     */
    protected $imageType;

    /**
     * User Agent String used on the page request
     *
     * @var string
     */
    protected $userAgentString = '';

    /**
     * Sets the timeout period
     *
     * @var int
     */
    protected $timeout = 0;
    
     /**
     * Sets the delay period
     *
     * @var int
     */
    protected $delay = 0;

    /**
     * Bin directory, should contain the phantomjs file, otherwise it won't work
     *
     * @var string
     */
    public $binPath;

    /**
     * Template directory, directory in which will be the js templates files to execute
     *
     * @var string
     */
    public $templatePath;

    /**
     * Jobs directory, directory for temporary files to be written and executed with phantomjs
     *
     * @var Jobs
     */
    public $jobs;

    /**
     * Base directory to save the output files
     *
     * @var Output
     */
    public $output;

    /**
     * Location where the file was written to
     *
     * @var string
     */
    protected $imageLocation;

    /**
     * List of included JS scripts
     *
     * @var array
     */
    protected $includedJsScripts = array();

    /**
     * List of included JS snippets
     *
     * @var array
     */
    protected $includedJsSnippets = array();

    /**
     * List of options which will be passed to phantomjs
     *
     * @var array
     */
    protected $options = array();

    /**
     * Capture constructor.
     */
    public function __construct($url = null)
    {
        if ($url) {
            $this->setUrl($url);
        }

        $this->binPath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'bin'))) . DIRECTORY_SEPARATOR;
        $this->templatePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'templates'))) . DIRECTORY_SEPARATOR;

        $this->jobs = new Jobs();
        $this->output = new Output();

        $this->setImageType(Types\Jpg::FORMAT);
    }

    /**
     * Saves the screenshot to the requested location
     *
     * @param string $imageLocation      Image Location
     * @param bool   $deleteFileIfExists True to delete the file if it exists
     *
     * @return bool
     */
    public function save($imageLocation, $deleteFileIfExists = true)
    {
        $this->imageLocation = $this->output->getLocation() . $imageLocation;

        if (!pathinfo($this->imageLocation, PATHINFO_EXTENSION)) {
            $this->imageLocation .= '.' . $this->getImageType()->getFormat();
        }

        $data = array(
            'url'           => (string) $this->url,
            'width'         => $this->width,
            'height'        => $this->height,
            'imageLocation' => LocalPath::sanitize($this->imageLocation),
        );

        if ($this->clipWidth && $this->clipHeight) {
            $data['clipOptions']['width'] = $this->clipWidth;
            $data['clipOptions']['height'] = $this->clipHeight;
            $data['clipOptions']['top'] = $this->top;
            $data['clipOptions']['left'] = $this->left;
        }

        if ($this->backgroundColor) {
            $data['backgroundColor'] = $this->backgroundColor;
        } elseif ($this->getImageType()->getFormat() == Types\Jpg::FORMAT) {
            // If there is no background color set, and it's a jpeg
            // we need to set a bg color, otherwise the background will be black
            $data['backgroundColor'] = '#FFFFFF';
        }

        if ($this->userAgentString) {
            $data['userAgent'] = $this->userAgentString;
        }

        if ($this->timeout) {
            $data['timeout'] = $this->timeout;
        }
        
        if ($this->delay) {
            $data['delay'] = $this->delay;
        }

        if ($this->includedJsScripts) {
            $data['includedJsScripts'] = $this->includedJsScripts;
        }

        if ($this->includedJsSnippets) {
            $data['includedJsSnippets'] = $this->includedJsSnippets;
        }

        if ($deleteFileIfExists && file_exists($this->imageLocation) && is_writable($this->imageLocation)) {
            unlink($this->imageLocation);
        }

        $jobName = md5(json_encode($data));
        $jobPath = $this->jobs->getLocation() . $jobName . '.js';

        if (!is_file($jobPath)) {
            // Now we write the code to a js file
            $resultString = $this->getTemplateResult('screen-capture', $data);
            file_put_contents($jobPath, $resultString);
        }

        $command = sprintf("%sphantomjs %s %s", $this->binPath, $this->getOptionsString(), $jobPath);

        // Run the command and ensure it executes successfully
        $returnCode = null;
        $output = [];
        exec(sprintf("%s 2>&1", escapeshellcmd($command)), $output, $returnCode);

        if ($returnCode !== 0) {
            throw new PhantomJsException($output);
        }

        return file_exists($this->imageLocation);
    }

    /**
     * @param string $templateName
     * @param array $args
     *
     * @return string
     * @throws TemplateNotFoundException
     */
    private function getTemplateResult($templateName, array $args)
    {
        $templatePath = $this->templatePath . DIRECTORY_SEPARATOR . $templateName . '.php';
        if (!file_exists($templatePath)) {
            throw new TemplateNotFoundException($templateName);
        }
        ob_start();
        extract($args);
        include $this->templatePath . DIRECTORY_SEPARATOR . $templateName . '.php';

        return ob_get_clean();
    }

    /**
     * @return string
     */
    private function getOptionsString()
    {
        if (empty($this->options)) {
            return '';
        }

        $mappedOptions = array_map(function ($value, $key) {
            if (substr($key, 0, 2) === '--') {
                $key = substr($key, 2);
            }

            return sprintf("--%s=%s", $key, $value);
        }, $this->options, array_keys($this->options));

        return implode(' ', $mappedOptions);
    }

    /**
     * Sets the path to PhantomJS binary, example: "/usr/bin"
     *
     * @param string $path
     */
    public function setBinPath($binPath)
    {
        $binPath = rtrim($binPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!file_exists($binPath . 'phantomjs') && !file_exists($binPath . 'phantomjs.exe')) {
            throw new \Exception("Bin directory should contain phantomjs or phantomjs.exe file!");
        }
        $this->binPath = $binPath;
    }

    /**
     * Sets the url to screenshot
     *
     * @param string $url URL
     *
     * @throws \Exception If the url is not valid
     */
    public function setUrl($url)
    {
        $this->url = new Url($url);
    }

    /**
     * Sets the dom top position
     *
     * @param int $top dom top position
     *
     * @return Capture
     */
    public function setTop($top)
    {
        $this->top = $top;

        return $this;
    }
    
    /**
     * Sets the page width
     *
     * @param int $left dom left position
     *
     * @return Capture
     */
    public function setLeft($left)
    {
        $this->left = $left;

        return $this;
    }
    
    /**
     * Sets the page width
     *
     * @param int $width Page Width
     *
     * @return Capture
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Sets the page height
     *
     * @param int $height Page Height
     *
     * @return Capture
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Sets the width to clip
     *
     * @param int $clipWidth Page clip width
     *
     * @return Capture
     */
    public function setClipWidth($clipWidth)
    {
        $this->clipWidth = $clipWidth;

        return $this;
    }

    /**
     * Sets the height to clip
     *
     * @param int $clipHeight Page clip height
     *
     * @return Capture
     */
    public function setClipHeight($clipHeight)
    {
        $this->clipHeight = $clipHeight;

        return $this;
    }

    /**
     * Sets the page body background color
     *
     * @param string $backgroundColor Background Color
     *
     * @return Capture
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    /**
     * Sets the image type
     *
     * @param string $type 'jpg', 'png', etc...
     *
     * @return Capture
     */
    public function setImageType($type)
    {
        $this->imageType = Types::getClass($type);

        return $this;
    }

    /**
     * Returns the image type instance
     *
     * @return Type
     */
    public function getImageType()
    {
        return $this->imageType;
    }

    /**
     * Returns the location where the screenshot file was written
     *
     * @return string
     */
    public function getImageLocation()
    {
        return $this->imageLocation;
    }

    /**
     * Sets the User Agent String to be used on the page request
     *
     * @param string $userAgentString User Agent String
     *
     * @return Capture
     */
    public function setUserAgentString($userAgentString)
    {
        $this->userAgentString = $userAgentString;

        return $this;
    }

    /**
     * Sets the timeout period
     *
     * @param int $timeout Timeout period
     *
     * @return Capture
     * @throws InvalidArgumentException
     */
    public function setTimeout($timeout)
    {
        if (!is_numeric($timeout)) {
            throw new InvalidArgumentException('The timeout value must be a number.');
        }
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Sets the delay period
     *
     * @param int $delay Delay period
     *
     * @return Capture
     * @throws InvalidArgumentException
     */
    public function setDelay($delay)
    {
        if (!is_numeric($delay)) {
            throw new InvalidArgumentException('The delay value must be a number.');
        }
        $this->delay = $delay;

        return $this;
    }
    /**
     * Adds a JS script or snippet to the screen shot script
     *
     * @param string|URL $script Script to include
     *
     * @return Capture
     */
    public function includeJs($script)
    {
        if ($script instanceof Url) {
            $this->includedJsScripts[] = $script;
        } else {
            $this->includedJsSnippets[] = $script;
        }

        return $this;
    }

    /**
     * Sets the options which will be passed to phantomjs
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }
}
