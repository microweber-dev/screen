<?php

namespace Screen\Location;

/**
 * Class Location
 *
 * @package Screen\Location
 * @author  André Filipe <andre.r.flip@gmail.com>
 * @license MIT https://github.com/microweber/screen/blob/master/LICENSE
 */
abstract class Location
{
    /**
     * Directory Path
     */
    protected string $location = '';


    /**
     * Sets the files location
     *
     * @throws \Exception If the location does not exist
     */
    public function setLocation(string $locationPath): void
    {
        $locationPath = realpath($locationPath);
        if (!$locationPath || !is_dir($locationPath)) {
            throw new \Exception("The directory '$locationPath' does not exist.");
        }

        $this->location = $locationPath . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the location path
     */
    public function getLocation(): string
    {
        return $this->location;
    }
}
