<?php

declare(strict_types=1);

namespace App\Contracts\Actions;

use App\Exceptions\FileNotFoundException;

interface FindsImages
{
    /**
     * @param  string[]  $only
     * @return string[] All image paths in $path
     *
     * @throws FileNotFoundException
     */
    public function __invoke(?string $path, array $only): array;
}
