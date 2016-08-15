<?php

namespace Screen\Injection;

use Screen\Exceptions\FileNotFoundException;

/**
 * Class LocalPath
 *
 * @package Screen\Injection
 */
class LocalPath extends Url
{

    /**
     * LocalPath constructor.
     *
     * @param string $url Local file path
     *
     * @throws FileNotFoundException
     */
    public function __construct($url)
    {
        $filePath = realpath($url);

        if (!$filePath || !file_exists($filePath)) {
            throw new FileNotFoundException($filePath);
        }

        $this->src = self::sanitize($filePath);
    }

    /**
     * Sanitizes a path string
     *
     * @param string $path File Path
     *
     * @return string
     */
    public static function sanitize($path)
    {
        // If used on windows the \ char needs to be handled to be used on a string
        return str_replace("\\", "\\\\", $path);
    }
}
