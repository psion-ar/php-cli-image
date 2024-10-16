<?php

declare(strict_types=1);

namespace App\Actions\Forms;

use App\Contracts\Actions\Forms\Components\SelectsImages;
use Illuminate\Support\LazyCollection;
use ReflectionMethod;
use Spatie\Image\Image;
use Webmozart\Assert\Assert;

use function Laravel\Prompts\form;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

final class RenderResizeAxisForm
{
    public function __construct(private SelectsImages $selectImages) {}

    /**
     * @param  string[]  $paths
     * @return array{images: LazyCollection<int, Image>, axis: ReflectionMethod, value: int}
     */
    public function __invoke(array $paths, bool $all): array
    {
        $responses = form()
            ->add(fn () => ($this->selectImages)($paths, $all), 'paths', true)
            ->add(fn () => $this->selectAxis(), 'axis')
            ->add(fn (array $responses) => $this->value($responses['axis']), 'value')
            ->submit();

        Assert::isList($responses['paths']);

        return [
            'images' => collect($responses['paths'])->mapInto(Image::class)->lazy(),
            'axis' => new ReflectionMethod(Image::class, $responses['axis']),
            'value' => $responses['value'],
        ];
    }

    private function selectAxis(): string
    {
        return (string) select(
            label: 'Select the axis to resize',
            options: ['width', 'height'],
        );
    }

    private function value(string $axis): int
    {
        return (int) text(
            label: sprintf('Enter the %s value', ucfirst($axis)),
            validate: ['value' => 'required|integer|min:1'],
            hint: 'The value must not be less than 1.',
        );
    }
}
