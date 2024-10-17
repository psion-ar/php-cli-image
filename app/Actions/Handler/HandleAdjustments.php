<?php

declare(strict_types=1);

namespace App\Actions\Handler;

use App\Contracts\Actions\SavesImage;
use App\DTOs\Request;
use App\Services\Metrics;
use ReflectionMethod;
use Spatie\Image\Image;

use function Laravel\Prompts\progress;

final class HandleAdjustments
{
    private Request $request;

    public function __construct(private SavesImage $save) {}

    public function __invoke(Request $request): Metrics
    {
        $this->request = $request;
        unset($request);

        $progress = fn () => progress(
            label: 'Processing images...',
            steps: $this->request->images,
            callback: function (Image $image) {
                $this->applyAdjustments($image);
                ($this->save)($image, $this->request->target, $this->request->format);
            },
        );

        return new Metrics($progress, $this->request->images->count());
    }

    private function applyAdjustments(Image $image): void
    {
        foreach ($this->request->values as $adjustment) {
            ['method' => $method, 'value' => $value] = $adjustment;

            assert($method instanceof ReflectionMethod);

            $method->invoke($image, $value);
        }
    }
}
