<?php

declare(strict_types=1);

namespace App\Contracts\Actions;

use App\Enums\ImageFormat;
use Spatie\Image\Image;

interface SavesImage
{
    public function __invoke(Image $image, ?string $target = null, ?ImageFormat $format = null): void;
}
