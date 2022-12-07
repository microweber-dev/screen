<?php

namespace Screen\Injection;

use Screen\Exceptions\FileNotFoundException;
use Screen\Exceptions\InvalidUrlException;

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
     * @throws FileNotFoundException
     * @throws InvalidUrlException
     */
    public function __construct(string $url)
    {
        $filePath = realpath($url);

        if (!$filePath || !file_exists($filePath)) {
            throw new FileNotFoundException($filePath);
        }

        $this->src = self::sanitize($filePath);
        parent::__construct($url);
    }

    /**
     * Sanitizes a path string
     */
    public static function sanitize(string $path): string
    {
        // If used on windows the \ char needs to be handled to be used on a string
        return str_replace("\\", "\\\\", $path);
    }
}
