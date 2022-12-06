<?php

namespace Screen\Image;

use Screen\Image\Types\Jpg;
use Screen\Image\Types\Png;
use Screen\Image\Types\Pdf;
use Screen\Image\Types\Type;

class Types
{
    protected static array $typesMap = [Jpg::FORMAT => \Screen\Image\Types\Jpg::class, Png::FORMAT => \Screen\Image\Types\Png::class, Pdf::FORMAT => \Screen\Image\Types\Pdf::class];

    /**
     * Returns all the available image types
     */
    public static function available(): array
    {
        return array_keys(static::$typesMap);
    }

    /**
     * Check if an image type is available
     */
    public static function isAvailable(string $type): bool
    {
        return in_array(strtolower($type), static::available());
    }

    /**
     * Returns an instance of the requested image type
     * @throws \Exception
     */
    public static function getClass(string $type): Type
    {
        if (!static::isAvailable($type)) {
            throw new \Exception(
                "Invalid image format '{$type}'. " .
                "Allowed formats are: " . implode(', ', static::available())
            );
        }

        $className = static::$typesMap[strtolower($type)];

        return new $className();
    }
}
