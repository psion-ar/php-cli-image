<?php

declare(strict_types=1);

namespace App\Actions\Forms\Components;

use App\Contracts\Actions\Forms\Components\AsksForNumeric;

use function Laravel\Prompts\text;

final class AskForNumeric implements AsksForNumeric
{
    /**
     * @param  'integer'|'numeric'  $type
     */
    public function __invoke(
        string $name,
        int|float $min,
        int|float $max,
        string $type = 'numeric'
    ): int|float {
        $response = text(
            label: sprintf('Enter the %s value', $name),
            validate: ['value' => 'required|'.$type.'|between:'.(string) $min.','.(string) $max],
        );

        // @phpstan-ignore-next-line
        return filter_var($response, FILTER_VALIDATE_INT)
            ?: filter_var($response, FILTER_VALIDATE_FLOAT);
    }
}
