<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\ImageFormat;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

final class ImageExtension implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_iterable($value)) {
            foreach ($value as $item) {
                $this->extensions($attribute, $item, $fail);
            }

            return;
        }

        $this->extensions($attribute, $value, $fail);
    }

    /**
     * Check the value against a list of extensions
     *
     * @throws InvalidArgumentException
     */
    private function extensions(string $attribute, mixed $value, Closure $fail): void
    {
        Assert::string($value);

        if (! in_array($value, ImageFormat::toArray(), true)) {
            $fail(sprintf(
                '%s "%s" is not a valid image extension.%sTry one of "%s".',
                ucfirst($attribute),
                $value,
                PHP_EOL,
                implode(', ', ImageFormat::toArray())
            ));
        }
    }
}
