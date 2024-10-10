<?php

declare(strict_types=1);

namespace App\Contracts\Actions;

use App\Exceptions\FileNotFound;

interface FindsImages
{
    /**
     * @param  string[]  $only
     * @return string[] All image paths in $path
     *
     * @throws FileNotFound
     */
    public function __invoke(?string $path, array $only): array;
}
