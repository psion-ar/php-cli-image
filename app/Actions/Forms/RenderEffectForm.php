<?php

declare(strict_types=1);

namespace App\Actions\Forms;

use App\Contracts\Actions\Forms\Components\SelectsImages;
use Illuminate\Support\LazyCollection;
use ReflectionMethod;
use Spatie\Image\Image;
use Webmozart\Assert\Assert;

use function Laravel\Prompts\form;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\text;

final class RenderEffectForm
{
    public function __construct(private SelectsImages $selectImages) {}

    /**
     * Render a form to collect user input
     *
     * @param  string[]  $paths
     * @return array{images: LazyCollection<int, Image>, effects: array<array{method: ReflectionMethod, value: int|null}>}
     */
    public function __invoke(array $paths, bool $all): array
    {
        $responses = form()
            ->add(fn () => ($this->selectImages)($paths, $all), 'paths', true)
            ->add(fn () => $this->selectEffects(), 'effects')
            ->add(fn (array $responses): array => $this->methodsAndValues($responses['effects']), 'values')
            ->submit();

        Assert::isArray($responses['paths']);

        return [
            'images' => collect($responses['paths'])->mapInto(Image::class)->lazy(),
            'effects' => $responses['values'],
        ];
    }

    /**
     * Select the effect methods
     *
     * @return string[]
     */
    private function selectEffects(): array
    {
        // @phpstan-ignore-next-line
        return multiselect(
            label: 'Select the effects to apply',
            options: ['blur', 'pixelate', 'greyscale', 'sepia', 'sharpen'],
            required: 'Select at least one effect.',
        );
    }

    /**
     * @param  string[]  $methods
     * @return array<array{method: ReflectionMethod, value: int|null}>
     */
    private function methodsAndValues(array $methods): array
    {
        $methods = collect($methods)
            ->map(fn (string $method) => new ReflectionMethod(Image::class, $method));

        return $methods->map(function (ReflectionMethod $method) {
            return $method->getNumberOfParameters() > 0
                 ? ['method' => $method, 'value' => $this->argumentValue($method)]
                 : ['method' => $method, 'value' => null];
        })->all();
    }

    private function argumentValue(ReflectionMethod $method): int
    {
        return (int) text(
            label: sprintf('Enter the %s effect value', ucfirst($method->getName())),
            placeholder: '0 - 100',
            validate: ['value' => 'required|integer|between:0,100']
        );
    }
}
