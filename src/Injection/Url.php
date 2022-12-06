<?php

namespace Screen\Injection;

use Screen\Exceptions\InvalidUrlException;

class Url implements \Stringable
{
    /**
     * URL source
     */
    protected string $src;

    /**
     * @throws InvalidUrlException
     */
    public function __construct(string $url)
    {
        // Prepend http:// if the url doesn't contain it
        if (!stristr($url, 'http://') && !stristr($url, 'https://')) {
            $url = 'http://' . $url;
        }

        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidUrlException($url);
        }

        $url = str_replace([';', '"', '<?'], '', strip_tags($url));
        $url = str_replace(['\077', '\''], [' ', '/'], $url);

        $this->src = $this->expandShortUrl($url);
    }

    public function __toString(): string
    {
        return $this->src;
    }

    public function expandShortUrl(string $url): string
    {
        $headers = get_headers($url, 1) ;
        if (array_key_exists('location', $headers)) {
            if ($headers['location'] != '') {
                return $headers['location'];
            }
        }
        return $url;
    }
}
