<?php

declare(strict_types=1);

namespace App\Actions\Handler;

use App\Contracts\Actions\SavesImage;
use App\DTOs\Request;
use App\Services\Metrics;
use ReflectionMethod;
use Spatie\Image\Image;

use function Laravel\Prompts\progress;

final class HandleResizeAxis
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
                $this->applyResizeAxis($image);
                ($this->save)($image, $this->request->target, $this->request->format);
            },
        );

        return new Metrics($progress, $this->request->images->count());
    }

    private function applyResizeAxis(Image $image): void
    {
        assert($this->request->axis instanceof ReflectionMethod);

        $this->request->axis->invoke($image, $this->request->value);
    }
}
