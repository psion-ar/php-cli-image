<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Forms\Components;

interface SelectsImages
{
    /**
     * Select image paths
     *
     * @param  string[]  $paths
     * @return string[]  Selected image paths
     */
    public function __invoke(array $paths, bool $all): array;
}
