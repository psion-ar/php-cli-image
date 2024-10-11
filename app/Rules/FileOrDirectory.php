<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

final class FileOrDirectory implements ValidationRule
{
    /**
     * @throws InvalidArgumentException
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        Assert::string($value);

        if (! file_exists($value)) {
            $fail(sprintf('The %s "%s" is not a valid file or directory path.', $attribute, $value));
        }
    }
}
