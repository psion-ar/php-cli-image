<?php

declare(strict_types=1);

use App\Enums\ImageFormat;

it('can convert the underlying cases to an array of strings')
    ->expect(fn () => ImageFormat::toArray())
    ->toEqual(['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'tiff', 'heic']);
