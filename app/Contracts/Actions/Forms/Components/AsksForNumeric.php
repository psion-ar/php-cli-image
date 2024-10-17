<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Forms\Components;

interface AsksForNumeric
{
    /**
     * @param  'integer'|'numeric'  $type
     */
    public function __invoke(
        string $name,
        int|float $min,
        int|float $max,
        string $type = 'numeric'
    ): int|float;
}
