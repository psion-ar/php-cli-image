<?php

declare(strict_types=1);

namespace App\Actions\Forms;

use App\Contracts\Actions\Forms\Components\AsksForNumeric;
use App\Contracts\Actions\Forms\Components\SelectsImages;
use Illuminate\Support\Arr;
use Illuminate\Support\LazyCollection;
use ReflectionMethod;
use Spatie\Image\Image;
use Webmozart\Assert\Assert;

use function Laravel\Prompts\form;

final class RenderAdjustmentForm
{
    public function __construct(
        private SelectsImages $selectImages,
        private AsksForNumeric $askForNumeric
    ) {}

    /**
     * @param  string[]  $paths
     * @return array{
     *      images: LazyCollection<int, Image>,
     *      values: array<array{method: ReflectionMethod, value: int|float}>
     * }
     */
    public function __invoke(array $paths, bool $all): array
    {
        $response = form()
            ->add(fn () => ($this->selectImages)($paths, $all), 'paths', true)
            ->multiselect(
                label: 'Select the adjustments to apply',
                options: ['brightness', 'contrast', 'gamma'],
                required: 'Select at least one adjustment.',
                name: 'adjustments',
            )
            ->add(fn (array $responses) => $this->getAdjustmentValues($responses['adjustments']), 'values')
            ->submit();

        Assert::isList($response['paths']);

        return [
            'images' => collect($response['paths'])->mapInto(Image::class)->lazy(),
            'values' => $response['values'],
        ];
    }

    /**
     * @param  string[]  $adjustments
     * @return array<array{method: ReflectionMethod, value: int|float}>
     */
    private function getAdjustmentValues(array $adjustments): array
    {
        return Arr::map($adjustments, function (string $adjustment) {
            return $adjustment !== 'gamma'
                ? [
                    'method' => new ReflectionMethod(Image::class, $adjustment),
                    'value' => ($this->askForNumeric)($adjustment, -100, 100, 'integer'),
                ]
                : [
                    'method' => new ReflectionMethod(Image::class, $adjustment),
                    'value' => ($this->askForNumeric)($adjustment, 0.1, 9.99),
                ];
        });
    }
}
