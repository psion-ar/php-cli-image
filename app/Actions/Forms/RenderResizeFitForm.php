<?php

declare(strict_types=1);

namespace App\Actions\Forms;

use App\Contracts\Actions\Forms\Components\SelectsImages;
use Illuminate\Support\Arr;
use Illuminate\Support\LazyCollection;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;
use Webmozart\Assert\Assert;

use function Laravel\Prompts\form;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

final class RenderResizeFitForm
{
    public function __construct(private SelectsImages $selectImages) {}

    /**
     * @param  string[]  $paths
     * @return array{
     *      images: LazyCollection<int, Image>,
     *      fit: Fit,
     *      width: int,
     *      height: int,
     *      background: string
     * }
     */
    public function __invoke(array $paths, bool $all): array
    {
        $responses = form()
            ->add(fn () => ($this->selectImages)($paths, $all), 'paths', true)
            ->add(fn () => $this->selectFit(), 'fit')
            ->add(fn () => $this->value('width'), 'width')
            ->add(fn () => $this->value('height'), 'height')
            ->add(fn (array $responses) => $this->background($responses['fit']), 'background')
            ->submit();

        Assert::isList($responses['paths']);

        return [
            'images' => collect($responses['paths'])->mapInto(Image::class)->lazy(),
            'fit' => $responses['fit'],
            'width' => $responses['width'],
            'height' => $responses['height'],
            'background' => $responses['background'],
        ];
    }

    private function selectFit(): Fit
    {
        return Fit::from((string) select(
            label: 'Select the fit method',
            options: Arr::map(Fit::cases(), fn (Fit $fit) => $fit->value),
        ));
    }

    private function value(string $axis): int
    {
        return (int) text(
            label: sprintf('Enter the %s value', $axis),
            validate: ['value' => 'required|integer|min:1'],
            hint: 'The value must not be less than 1.',
        );
    }

    private function background(Fit $fit): ?string
    {
        return $fit === Fit::Fill || $fit === Fit::FillMax
            ? text(
                label: 'Enter background color hex value',
                placeholder: 'hex color code, (e.g. #000000)',
                validate: ['value' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i']],
            )
            : null;
    }
}
