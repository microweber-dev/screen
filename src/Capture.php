<?php

namespace Screen;

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
     * Image format
     *
     * @var string
     */
    protected $format = 'jpg';

    /**
     * User Agent String used on the page request
     *
     * @var string
     */
    protected $userAgentString = '';

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
    }

    public function save($imageLocation, $deleteFileIfExists = true)
    {
        $outputPath = $this->output->getLocation() . $imageLocation;

        $data = array(
            'url'           => $this->url,
            'width'         => $this->width,
            'height'        => $this->height,
            // If used on windows the \ char needs to be handled to be used on a js string
            'imageLocation' => str_replace("\\", "\\\\", $outputPath),
        );

        if ($this->clipWidth && $this->clipHeight) {
            $data['clipOptions']['width'] = $this->clipWidth;
            $data['clipOptions']['height'] = $this->clipHeight;
            $data['clipOptions']['top'] = 0;
            $data['clipOptions']['left'] = 0;
        }

        if ($this->backgroundColor) {
            $data['backgroundColor'] = $this->backgroundColor;
        } elseif ($this->getFormat() == 'jpg') {
            // If there is no background color set, and it's a jpeg
            // we need to set a bg color, otherwise the background will be black
            $data['backgroundColor'] = '#FFFFFF';
        }

        if ($this->userAgentString) {
            $data['userAgent'] = $this->userAgentString;
        }

        if ($deleteFileIfExists && file_exists($outputPath)) {
            unlink($outputPath);
        }

        $jobName = md5(json_encode($data));
        $jobPath = $this->jobs->getLocation() . $jobName . '.js';

        if (!is_file($jobPath)) {
            // Now we write the code to a js file
            $resultString = $this->getTemplateResult('screen-capture', $data);
            file_put_contents($jobPath, $resultString);
        }

        $command = sprintf("%sphantomjs %s", $this->binPath, $jobPath);
        $result = exec(escapeshellcmd($command));

        return file_exists($outputPath);
    }

    private function getTemplateResult($templateName, array $args)
    {
        $templatePath = $this->templatePath . DIRECTORY_SEPARATOR . $templateName . '.php';
        if (!file_exists($templatePath)) {
            throw new \Exception("The template {$templateName} does not exist!");
        }
        ob_start();
        extract($args);
        include $this->templatePath . DIRECTORY_SEPARATOR . $templateName . '.php';

        return ob_get_clean();
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
        // Prepend http:// if the url doesn't contain it
        if (!stristr($url, 'http://') && !stristr($url, 'https://')) {
            $url = 'http://' . $url;
        }

        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception("Invalid URL");
        }

        $url = str_replace(array(';', '"', '<?'), '', strip_tags($url));
        $url = str_replace(array('\077', '\''), array(' ', '/'), $url);

        $this->url = $url;
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
     * Sets the image format
     *
     * @param string $format  'jpg' | 'png'
     *
     * @return Capture
     */
    public function setFormat($format)
    {
        $format = strtolower($format);
        if (!in_array($format, ['jpg', 'png'])) {
            throw new Exception(
                "Invalid image format '{$format}'. " .
                "Allowed formats are 'jpg' and 'png'"
            );
        }

        $this->format = $format;

        return $this;
    }

    /**
     * Gets the image format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Gets the MIME type of resulted image
     *
     * @return string
     */
    public function getMimeType()
    {
        if ($this->format === 'png') {
            return 'image/png';
        }

        return 'image/jpeg';
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
}
