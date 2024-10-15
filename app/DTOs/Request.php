<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\ImageFormat;
use Illuminate\Support\LazyCollection;
use InvalidArgumentException;
use Spatie\Image\Image;
use Webmozart\Assert\Assert;

final class Request
{
    public string $command;

    public ?string $target;

    public ?ImageFormat $format;

    /**
     * @var LazyCollection<int, Image>
     */
    public LazyCollection $images;

    /**
     * @var array<string, mixed>
     */
    private array $properties = [];

    /**
     * Create a new request statically
     *
     * @param  array<string, mixed>  $values
     */
    public static function create(array $values): self
    {
        $self = new self;

        foreach ($values as $key => $value) {
            $self->$key = $value;
        }

        return $self;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function __set(string $name, mixed $value): void
    {
        Assert::keyNotExists($this->properties, $name);

        $this->properties[$name] = $value;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function __get(string $name): mixed
    {
        Assert::keyExists($this->properties, $name);

        return $this->properties[$name];
    }
}
