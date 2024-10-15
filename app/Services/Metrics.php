<?php

declare(strict_types=1);

namespace App\Services;

use Closure;
use Illuminate\Support\Str;
use Webmozart\Assert\Assert;
use InvalidArgumentException;
use Illuminate\Support\Number;
use Illuminate\Support\Benchmark;

final class Metrics
{
    private Closure $benchmarkable;

    private float $milliseconds;

    private int $items;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(Closure $benchmarkable, int $items)
    {
        Assert::greaterThanEq($items, 1);

        $this->benchmarkable = $benchmarkable;
        $this->items = $items;

        $this->benchmark();
    }

    public function get(): string
    {
        return sprintf(
            '[Duration:] %s / %d %s%s[Average: ] %s / image',
            $this->duration(),
            $this->items,
            Str::plural('image', $this->items),
            PHP_EOL,
            $this->average()
        );
    }

    private function duration(): string
    {
        return Number::format($this->milliseconds / 1000, 2) . ' s';
    }

    private function average(): string
    {
        return Number::format($this->milliseconds / $this->items / 1000, 2) . ' s';
    }

    private function benchmark(): void
    {
        $this->milliseconds = Benchmark::measure($this->benchmarkable);
    }
}
