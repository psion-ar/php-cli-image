<?php

declare(strict_types=1);

namespace App\Actions\Handler;

use App\Contracts\Actions\SavesImage;
use App\DTOs\Request;
use App\Services\Metrics;
use Spatie\Image\Image;

use function Laravel\Prompts\progress;

final class HandleResizeFit
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
                $this->applyResizeFit($image);
                ($this->save)($image, $this->request->target, $this->request->format);
            },
        );

        return new Metrics($progress, $this->request->images->count());
    }

    private function applyResizeFit(Image $image): void
    {
        $image->fit(
            fit: $this->request->fit,
            desiredWidth: $this->request->width,
            desiredHeight: $this->request->height,
            backgroundColor: $this->request->background ?? '#ffffff',
        );
    }
}
