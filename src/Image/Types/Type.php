<?php

namespace Screen\Image\Types;

abstract class Type
{
    public const FORMAT = '';

    public const MIME_TYPE = '';

    /**
     * Gets the image format
     */
    public function getFormat(): string
    {
        return static::FORMAT;
    }

    /**
     * Gets the MIME type of resulted image
     */
    public function getMimeType(): string
    {
        return static::MIME_TYPE;
    }
}
