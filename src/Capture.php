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
use Screen\CookieJar;

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
     */
    protected string $url;

    /**
     * dom element top position
     */
    protected string $top;

    /**
     * dom element left position
     */
    protected string $left;

    /**
     * Width of the page to render
     */
    protected int $width = 1024;

    /**
     * Height of the page to render
     */
    protected int $height = 768;

    /**
     * Width of the page to clip
     */
    protected int $clipWidth;

    /**
     * Height of the page to clip
     */
    protected int $clipHeight;

    /**
     * Default body background color is white
     */
    protected string $backgroundColor = '';

    /**
     * Image Type, default is jpeg
     */
    protected Type $imageType;

    /**
     * User Agent String used on the page request
     */
    protected string $userAgentString = '';

    /**
     * Sets the option to block analytics from being pinged
     */
    protected bool $blockAnalytics = false;

    /**
     * Sets the timeout period
     */
    protected int $timeout = 0;

    /**
     * Sets the delay period
     */
    protected int $delay = 0;

    /**
     * Bin directory, should contain the phantomjs file, otherwise it won't work
     */
    public string $binPath;

    /**
     * Template directory, directory in which will be the js templates files to execute
     */
    public string $templatePath;

    /**
     * Jobs directory, directory for temporary files to be written and executed with phantomjs
     */
    public Jobs $jobs;

    /**
     * Base directory to save the output files
     */
    public Output $output;

    /**
     * Location where the file was written to
     */
    protected string $imageLocation;

    /**
     * List of included JS scripts
     */
    protected array $includedJsScripts = [];

    /**
     * List of included JS snippets
     */
    protected array $includedJsSnippets = [];

    /**
     * List of options which will be passed to phantomjs
     */
    protected array $options = [];

    /**
     * Sets to keep the cookies between save().
     */
    protected bool $keepCookies = false;

    /**
     * CookieJar to put cookies saved.
     */
    public CookieJar $cookieJar;

    /**
     * Capture constructor.
     */
    public function __construct(?string $url = null)
    {
        if ($url) {
            $this->setUrl($url);
        }

        $this->binPath = realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'bin'])) . DIRECTORY_SEPARATOR;
        $this->templatePath = realpath(
                implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'templates'])
            ) . DIRECTORY_SEPARATOR;

        $this->jobs = new Jobs();
        $this->output = new Output();
        $this->cookieJar = new CookieJar();

        $this->setImageType(Types\Jpg::FORMAT);
    }

    /**
     * Saves the screenshot to the requested location
     *
     * @param string $imageLocation Image Location
     * @param bool $deleteFileIfExists True to delete the file if it exists
     * @throws PhantomJsException
     */
    public function save(string $imageLocation, bool $deleteFileIfExists = true): bool
    {
        $this->imageLocation = $this->output->getLocation() . $imageLocation;

        if (!pathinfo($this->imageLocation, PATHINFO_EXTENSION)) {
            $this->imageLocation .= '.' . $this->getImageType()->getFormat();
        }

        $data = [
            'url' => $this->url,
            'width' => $this->width,
            'height' => $this->height,
            'imageLocation' => LocalPath::sanitize($this->imageLocation)
        ];

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

        if ($this->blockAnalytics) {
            $data['blockAnalytics'] = $this->blockAnalytics;
        }

        if ($deleteFileIfExists && file_exists($this->imageLocation) && is_writable($this->imageLocation)) {
            unlink($this->imageLocation);
        }

        if ($this->keepCookies) {
            // Take the JSON cookies and put in the array to be write in js.
            $data['cookieJar'] = $this->cookieJar->getCookiesJSON();
        }

        $jobName = md5(json_encode($data, JSON_THROW_ON_ERROR));
        $jobPath = $this->jobs->getLocation() . $jobName . '.js';

        // Saves the cookies in the same folder as the jobs.
        $cookiesPath = $this->jobs->getLocation() . $jobName . '.json';
        // Put the path in array. The js will pick up this filepath and save the cookies in it.
        $data['cookiesPath'] = LocalPath::sanitize($cookiesPath);

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

        if ($this->keepCookies) {
            $this->cookieJar->load($cookiesPath);
            unlink($cookiesPath);
        }

        if ($returnCode !== 0) {
            throw new PhantomJsException($output);
        }

        return file_exists($this->imageLocation);
    }

    /**
     * @throws TemplateNotFoundException
     */
    private function getTemplateResult(string $templateName, array $args): string
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

    private function getOptionsString(): string
    {
        if (empty($this->options)) {
            return '';
        }

        $mappedOptions = array_map(function ($value, $key) {
            if (str_starts_with($key, '--')) {
                $key = substr($key, 2);
            }

            return sprintf("--%s=%s", $key, $value);
        }, $this->options, array_keys($this->options));

        return implode(' ', $mappedOptions);
    }

    /**
     * Sets the path to PhantomJS binary, example: "/usr/bin"
     * @throws \Exception
     */
    public function setBinPath(string $binPath): void
    {
        $binPath = rtrim((string)$binPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!file_exists($binPath . 'phantomjs') && !file_exists($binPath . 'phantomjs.exe')) {
            throw new \Exception("Bin directory should contain phantomjs or phantomjs.exe file!");
        }
        $this->binPath = $binPath;
    }

    /**
     * Sets the url to screenshot
     * @throws \Exception If the url is not valid
     *
     */
    public function setUrl(string $url): Capture
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Sets the dom top position
     *
     * @param int $top dom top position
     */
    public function setTop(int $top): Capture
    {
        $this->top = $top;

        return $this;
    }

    /**
     * Sets the page width
     *
     * @param int $left dom left position
     */
    public function setLeft(int $left): Capture
    {
        $this->left = $left;

        return $this;
    }

    /**
     * Sets the page width
     *
     * @param int $width Page Width
     */
    public function setWidth(int $width): Capture
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Sets the page height
     *
     * @param int $height Page Height
     */
    public function setHeight(int $height): Capture
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Sets the width to clip
     *
     * @param int $clipWidth Page clip width
     */
    public function setClipWidth(int $clipWidth): Capture
    {
        $this->clipWidth = $clipWidth;

        return $this;
    }

    /**
     * Sets the height to clip
     *
     * @param int $clipHeight Page clip height
     */
    public function setClipHeight(int $clipHeight): Capture
    {
        $this->clipHeight = $clipHeight;

        return $this;
    }

    /**
     * Sets the page body background color
     *
     * @param string $backgroundColor Background Color
     */
    public function setBackgroundColor(string $backgroundColor): Capture
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    /**
     * Sets the image type
     *
     * @param string $type 'jpg', 'png', etc...
     * @throws \Exception
     */
    public function setImageType(string $type): Capture
    {
        $this->imageType = Types::getClass($type);

        return $this;
    }

    /**
     * Sets the block analytics type
     */
    public function setBlockAnalytics(bool $boolean): Capture
    {
        $this->blockAnalytics = $boolean;

        return $this;
    }

    /**
     * Returns the block analytics instance
     */
    public function getBlockAnalytics(): Type|bool
    {
        return $this->blockAnalytics;
    }

    /**
     * Returns the image type instance
     */
    public function getImageType(): Type
    {
        return $this->imageType;
    }

    /**
     * Returns the location where the screenshot file was written
     */
    public function getImageLocation(): string
    {
        return $this->imageLocation;
    }

    /**
     * Sets the User Agent String to be used on the page request
     */
    public function setUserAgentString(string $userAgentString): Capture
    {
        $this->userAgentString = $userAgentString;

        return $this;
    }

    /**
     * Sets the timeout period
     *
     * @throws InvalidArgumentException
     */
    public function setTimeout(int $timeout): Capture
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
     * @throws InvalidArgumentException
     */
    public function setDelay(int $delay): Capture
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
     */
    public function includeJs($script): Capture
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
     */
    public function setOptions(array $options): Capture
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Sets to keep the cookies between save().
     */
    public function keepCookiesBetweenSave(bool $choice): Capture
    {
        $this->keepCookies = $choice;
        return $this;
    }
}
