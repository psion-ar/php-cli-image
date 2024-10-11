<?php

declare(strict_types=1);

namespace App\Actions\Forms\Components;

use App\Contracts\Actions\Forms\Components\SelectsImages;
use Illuminate\Support\Arr;

use function Laravel\Prompts\multiselect;

final class SelectImages implements SelectsImages
{
    public function __invoke(array $paths, bool $all): array
    {
        if ($all || count($paths) === 1) {
            return $paths;
        }

        // @phpstan-ignore-next-line
        return multiselect(
            label: 'Select the images to process',
            options: Arr::collapse(Arr::map($paths, fn (string $path) => [$path => basename($path)])),
            required: 'Select at least one image path.',
        );
    }
}
