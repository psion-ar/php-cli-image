<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\Actions\SavesImage;
use App\Enums\ImageFormat;
use Illuminate\Support\Facades\File;
use Spatie\Image\Image;

final class SaveImage implements SavesImage
{
    private Image $image;

    private string $originalPath;

    private string $savePath;

    public function __invoke(
        Image $image,
        ?string $target = null,
        ?ImageFormat $format = null
    ): void {
        $this->image = $image;
        unset($image);

        $this->originalPath()->savePath($target, $format)->optimize()->save($this->savePath);
    }

    /**
     * Save image.
     */
    private function save(string $path): void
    {
        $this->image->save($this->ensureUniqueFilename($path));
    }

    /**
     * Run optimizer chain.
     */
    private function optimize(): static
    {
        $this->image->optimize();

        return $this;
    }

    /**
     * Build and set save path.
     */
    private function savePath(?string $target, ?ImageFormat $format): static
    {
        if ($target === null) {
            $dir = dirname($this->originalPath);
        } else {
            realpath($target) ?: File::ensureDirectoryExists($target);
            $dir = $target;
        }

        if ($format === null) {
            $filename = pathinfo($this->originalPath, PATHINFO_BASENAME);
        } else {
            $filename = sprintf('%s.%s', pathinfo($this->originalPath, PATHINFO_FILENAME), $format->value);
        }

        $this->savePath = sprintf('%s%s%s', $dir, DIRECTORY_SEPARATOR, $filename);

        return $this;
    }

    /**
     * Ensure unique filename
     */
    private function ensureUniqueFilename(string $path): string
    {
        $counter = 1;
        $filename = File::name($path);
        $extension = File::extension($path);

        while (file_exists($path)) {
            $path = sprintf('%s%s%s-%d.%s', File::dirname($path), DIRECTORY_SEPARATOR, $filename, $counter, $extension);
            $counter++;
        }

        return $path;
    }

    /**
     * Extract original file path from image object
     */
    private function originalPath(): static
    {
        $this->originalPath = getReflectionPropertyValue( // @phpstan-ignore assign.propertyType
            object: getReflectionPropertyValue($this->image, 'imageDriver'), // @phpstan-ignore argument.type
            property: 'originalPath'
        );

        return $this;
    }
}
