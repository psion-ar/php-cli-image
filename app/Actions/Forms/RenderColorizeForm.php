<?php

declare(strict_types=1);

namespace App\Actions\Forms;

use App\Contracts\Actions\Forms\Components\AsksForNumeric;
use App\Contracts\Actions\Forms\Components\SelectsImages;
use Illuminate\Support\LazyCollection;
use Spatie\Image\Image;
use Webmozart\Assert\Assert;

use function Laravel\Prompts\form;

final class RenderColorizeForm
{
    public function __construct(
        private SelectsImages $selectImages,
        private AsksForNumeric $askForNumeric
    ) {}

    /**
     * @param  string[]  $paths
     * @return array{
     *      images: LazyCollection<int, Image>,
     *      red: int,
     *      green: int,
     *      blue: int
     * }
     */
    public function __invoke(array $paths, bool $all): array
    {
        $responses = form()
            ->add(fn () => ($this->selectImages)($paths, $all), 'paths', true)
            ->add(fn () => ($this->askForNumeric)('red', -100, 100, 'integer'), 'red')
            ->add(fn () => ($this->askForNumeric)('green', -100, 100, 'integer'), 'green')
            ->add(fn () => ($this->askForNumeric)('blue', -100, 100, 'integer'), 'blue')
            ->submit();

        Assert::isList($responses['paths']);

        return [
            'images' => collect($responses['paths'])->mapInto(Image::class)->lazy(),
            'red' => $responses['red'],
            'green' => $responses['green'],
            'blue' => $responses['blue'],
        ];
    }
}
