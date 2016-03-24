<?php

namespace Screen;

/**
 * Class Jobs
 *
 * @package Screen
 * @author  André Filipe <andre.r.flip@gmail.com>
 * @license MIT https://github.com/microweber/screen/blob/master/LICENSE
 */
class Jobs
{
    /**
     * Jobs directory, directory for temporary files to be written and executed with phantomjs
     *
     * @var string
     */
    public $jobsPath;

    /**
     * Jobs constructor.
     */
    public function __construct()
    {
        $this->jobsPath = implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', 'jobs'));
        if (!is_dir($this->jobsPath)) {
            mkdir($this->jobsPath, 0755);
        }
        $this->jobsPath = realpath($this->jobsPath) . DIRECTORY_SEPARATOR;
    }

    /**
     * Deletes all the job files
     */
    public function clean()
    {
        $jobFiles = glob($this->jobsPath . '*.js');
        foreach ($jobFiles as $file) {
            unlink($file);
        }

        return $this;
    }

    /**
     * Sets the job files location
     *
     * @param string $path Path to store the job files
     *
     * @throws \Exception If the location does not exist
     *
     * @return Jobs
     */
    public function setLocation($path)
    {
        $path = realpath($path);
        if (!$path || !is_dir($path)) {
            throw new \Exception('This location does not exist.');
        }

        $this->jobsPath = $path . DIRECTORY_SEPARATOR;

        return $this;
    }

    /**
     * Returns the job location path
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->jobsPath;
    }
}
