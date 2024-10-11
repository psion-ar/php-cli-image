<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\Actions\FindsImages;
use App\Exceptions\FileNotFoundException;

class FindImages implements FindsImages
{
    private string $path;

    /**
     * @var string[]
     */
    private array $only = [];

    /**
     * Find all images in $path or fail.
     *
     * @param  string[]  $only
     * @return string[] All image paths in $path
     *
     * @throws FileNotFoundException
     */
    public function __invoke(?string $path, array $only): array
    {
        $this->path = $this->getRealPath($this->handleNullPath($path));
        unset($path);
        $this->only = $only;
        unset($only);

        if (is_image($this->path)) {
            return [$this->path];
        }

        $paths = collect($this->allFiles())
            ->filter(is_image(...))
            ->all();

        return $paths !== [] ? $paths : throw new FileNotFoundException(sprintf('No images found in "%s"', $this->path));
    }

    /**
     * Get all files from $this->path
     *
     * @return string[]
     */
    private function allFiles(): array
    {
        return glob(sprintf('%s%s*.%s', $this->path, DIRECTORY_SEPARATOR, $this->pattern()), GLOB_BRACE | GLOB_NOSORT) ?: [];
    }

    /**
     * Returns the current working directory if $path is null.
     *
     * @throws \RuntimeException
     */
    private function handleNullPath(?string $path): string
    {
        if ($path === null) {
            return getcwd() ?: throw new \RuntimeException('Could\'nt get current working directory');
        }

        return $path;
    }

    /**
     * Returns the canonicalized absolute pathname of $path.
     *
     * The resulting path will have no symbolic links, relative path
     * components, or redundant separators.
     *
     * @throws \RuntimeException If $path does not exist
     */
    private function getRealPath(string $path): string
    {
        return realpath($path) ?: throw new \RuntimeException(sprintf('Path "%s" does not exist', $path));
    }

    /**
     * Builds a glob pattern from $this->only
     */
    private function pattern(): string
    {
        return $this->only !== [] ? sprintf('{%s}', implode(',', $this->only)) : '*';
    }
}
