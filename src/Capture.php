<?php

namespace Screen;

/**
 * Class Capture
 *
 * @package Screen
 * @author André Filipe
 * @license BSD https://github.com/ariya/phantomjs/blob/master/LICENSE.BSD
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
     * @var string
     *
     * @todo Create job files in the temporary dir
     */
    public $jobsPath;

    /**
     * Capture constructor.
     *
     * @param string $url URL
     *
     * @throws \Exception If the url is not valid
     */
    public function __construct($url)
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

        $this->binPath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'bin'))) . DIRECTORY_SEPARATOR;
        $this->templatePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'templates'))) . DIRECTORY_SEPARATOR;
        $this->jobsPath = implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'jobs'));
        if (!is_dir($this->jobsPath)) {
            mkdir($this->jobsPath, 0755);
        }
        $this->jobsPath = realpath($this->jobsPath) . DIRECTORY_SEPARATOR;
    }

    public function save($imageLocation, $deleteFileIfExists = true)
    {
        $data = array(
            'url'           => $this->url,
            'width'         => $this->width,
            'height'        => $this->height,
            'clipWidth'     => $this->clipWidth,
            'clipHeight'    => $this->clipHeight,
            'imageLocation' => $imageLocation,
        );

        if ($deleteFileIfExists && file_exists($imageLocation)) {
            unlink($imageLocation);
        }

        $jobName = md5(json_encode($data));
        $jobPath = $this->jobsPath . $jobName . '.js';

        if (!is_file($jobPath)) {
            // Now we write the code to a js file
            $resultString = $this->getTemplateResult('screen-capture', $data);
            file_put_contents($jobPath, $resultString);
        }

        $command = sprintf("%sphantomjs %s", $this->binPath, $jobPath);
        exec(escapeshellcmd($command));

        return file_exists($imageLocation);
    }

    private function getTemplateResult($templateName, array $args)
    {
        $templatePath = $this->templatePath . DIRECTORY_SEPARATOR . $templateName . '.php';
        if (!file_exists($templatePath)) {
            throw new \Exception("The template {$templateName} does not exist!");
        }
        ob_start();
        extract($args);
        include_once $this->templatePath . DIRECTORY_SEPARATOR . $templateName . '.php';

        return ob_get_clean();
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
}
