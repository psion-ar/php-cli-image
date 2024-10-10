<?php

declare(strict_types=1);

namespace App\Enums;

enum ImageFormat: string
{
    case JPG = 'jpg';
    case JPEG = 'jpeg';
    case PNG = 'png';
    case GIF = 'gif';
    case WEBP = 'webp';
    case AVIF = 'avif';
    case TIFF = 'tiff';
    case HEIC = 'heic';

    /**
     * Returns an array of all available image formats.
     *
     * @return string[]
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
