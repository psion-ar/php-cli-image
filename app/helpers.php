<?php

declare(strict_types=1);

if (! function_exists('is_image')) {
    function is_image(string $path): bool
    {
        if (is_dir($path)) {
            return false;
        }

        if (! file_exists($path)) {
            return false;
        }

        if (filesize($path) < 12) {
            return false;
        }

        if (mime_content_type($path) === 'image/heic') {
            return true;
        }

        return exif_imagetype($path) ? true : false;
    }
}

if (! function_exists('getReflectionPropertyValue')) {
    function getReflectionPropertyValue(object $object, string $property): mixed
    {
        $property = new ReflectionProperty($object, $property);

        return $property->getValue($object);
    }
}
