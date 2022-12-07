<?php

namespace Screen;

use Screen\Exceptions\FileNotFoundException;

/**
 * Class CookieJar
 *
 * @package Screen
 * @author  André Filipe <andre.r.flip@gmail.com>
 * @license MIT https://github.com/microweber/screen/blob/master/LICENSE
 */
class CookieJar
{

    /**
     * Cookies in JSON format.
     */
    protected string $cookies;

    /**
     * Load cookies from file
     *
     * @param string $file Path to file
     * @throws FileNotFoundException
     */
    public function load(string $file)
    {
        $realPath = realpath($file);

        if (!$realPath || !file_exists($realPath)) {
            throw new FileNotFoundException($realPath);
        }

        $this->cookies = file_get_contents($realPath);
    }

    /**
     * Gets Cookies on JSON format
     */
    public function getCookiesJSON(): string
    {
        return $this->cookies;
    }

    /**
     * Sets Cookies on JSON format
     */
    public function setCookiesJSON(string $cookiesJSON): void
    {
        $this->cookies = $cookiesJSON;
    }

    /**
     * Clear cookies from cookieJar.
     */
    public function empty(): void
    {
        $this->cookies = null;
    }
}
