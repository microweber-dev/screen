<?php

namespace Screen;

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
    *
    * @var string
    */
    protected $cookies;

    /**
     * Load cookies from file
     *
     * @param string $file    Path to file
     */
    public function load($file){
        $realPath = realpath($file);

        if (!$realPath || !file_exists($realPath)) {
            throw new FileNotFoundException($realPath);
        }

        $this->cookies = file_get_contents($realPath);     
    }

    /**
     * Gets Cookies on JSON format
     *
     * @return string
     */
    public function getCookiesJSON(){
        return $this->cookies;
    }

    /**
     * Sets Cookies on JSON format
     *
     * @param string
     */
    public function setCookiesJSON($cookiesJSON){
        $this->cookies = $cookiesJSON;
    }

    /**
     * Clear cookies from cookieJar.
     */
    public function empty(){
        $this->cookies = null;
    }
}
