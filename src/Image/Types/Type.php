<?php

namespace Screen\Image\Types;

abstract class Type
{
    const FORMAT = '';

    const MIME_TYPE = '';

    /**
     * Gets the image format
     *
     * @return string
     */
    public function getFormat()
    {
        return static::FORMAT;
    }

    /**
     * Gets the MIME type of resulted image
     *
     * @return string
     */
    public function getMimeType()
    {
        return static::MIME_TYPE;
    }
}
