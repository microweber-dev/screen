<?php

namespace Screen\Injection;

use Screen\Exceptions\InvalidUrlException;

class Url
{
    /**
     * URL source
     *
     * @var string
     */
    protected $src;

    public function __construct($url)
    {
        // Prepend http:// if the url doesn't contain it
        if (!stristr($url, 'http://') && !stristr($url, 'https://')) {
            $url = 'http://' . $url;
        }

        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidUrlException($url);
        }

        $url = str_replace(array(';', '"', '<?'), '', strip_tags($url));
        $url = str_replace(array('\077', '\''), array(' ', '/'), $url);

        $this->src = $url;
    }

    public function __toString()
    {
        return $this->src;
    }
}
