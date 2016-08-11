<?php

namespace Screen\Injection;

use Screen\Exceptions\FileNotFoundException;

class LocalPath extends Url
{
    public function __construct($url)
    {
        $filePath = realpath($url);

        if (!$filePath || !file_exists($filePath)) {
            throw new FileNotFoundException($filePath);
        }

        $this->src = $filePath;
    }
}
