<?php

declare(strict_types=1);

use App\Contracts\Actions\SavesImage;
use App\Enums\ImageFormat;
use Illuminate\Support\Facades\File;
use Ramsey\Uuid\Uuid;
use Spatie\Image\Image;

describe('SaveImageTest', function () {
    beforeEach(function (): void {
        $this->tempDir->empty();
    });

    it('saves an image', function () {
        // arrange
        $actual = getTempPath('test.jpg');
        $target = getTempPath('saved.jpg');
        copy(getTestJpg(), $actual);
        $image = Image::load($actual);
        $save = app(SavesImage::class);

        // act & assert
        assert($save instanceof SavesImage);
        $save(image: $image, target: $target);

        expect(File::exists($target))->toBeTrue();
    });

    test('when no target is provided it is saved to the same directory without overwriting', function () {
        // arrange
        $actual = getTempPath('test.jpg');
        copy(getTestJpg(), $actual);
        $image = Image::load($actual);
        $save = app(SavesImage::class);

        $expected = getTempPath('test-1.jpg');

        expect(File::exists($actual))->toBeTrue();
        expect(File::exists($expected))->toBeFalse();

        // act & assert
        assert($save instanceof SavesImage);
        $save(image: $image);

        expect(File::exists($actual))->toBeTrue();
        expect(File::exists($expected))->toBeTrue();
    });

    it('saves files in a specific format', function (string $format) {
        // arrange
        $actual = getTempPath('test.jpg');
        $target = $this->tempDir->path(Uuid::uuid4()->toString());
        copy(getTestJpg(), $actual);
        $image = Image::load($actual);
        $save = app(SavesImage::class);

        $expected = $target.DIRECTORY_SEPARATOR.'test.'.$format;

        expect(File::exists($actual))->toBeTrue();
        expect(File::exists($expected))->toBeFalse();

        // act & assert
        assert($save instanceof SavesImage);
        $save(image: $image, target: $target, format: ImageFormat::from($format));

        expect(File::exists($actual))->toBeTrue();
        expect(File::exists($expected))->toBeTrue();
        expect($expected)->toHaveMime('image/'.match ($format) {
            'jpg' => 'jpeg',
            default => $format,
        });
    })->with(ImageFormat::toArray());

    it('optimizes image on save', function () {
        // arrange
        $actual = getTempPath('test.jpg');
        copy(getTestJpg(), $actual);
        $image = Image::load($actual);
        $save = app(SavesImage::class);

        $expected = getTempPath('test-1.jpg');

        // act & assert
        assert($save instanceof SavesImage);
        $save(image: $image);

        expect(filesize($actual))->toBeGreaterThan(filesize($expected));
    });
});
