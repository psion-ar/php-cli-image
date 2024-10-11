<?php

declare(strict_types=1);

use App\Contracts\Actions\FindsImages;
use App\Enums\ImageFormat;
use App\Exceptions\FileNotFoundException;
use Illuminate\Support\Facades\File;

describe('FindImagesTest', function (): void {
    beforeEach(function (): void {
        $this->tempDir->empty();
    });

    it('finds from image paths')
        ->expect(fn () => app(FindsImages::class)(path: getTestJpg(), only: []))
        ->toEqual([getTestJpg()]);

    it('finds from directory path')
        ->expect(fn () => app(FindsImages::class)(path: getTestSupportPath('TestFiles'), only: []))
        ->toEqual(getAllTestFiles());

    it('resolves a null path to the current working directory', function (): void {
        // arrange
        chdir(getTestSupportPath('TestFiles'));

        // act & assert
        $actual = app(FindsImages::class)(path: null, only: []);
        $expected = getAllTestFiles();

        expect($actual)->toEqual($expected);
    });

    it('filters by format', function (string $format): void {
        expect(app(FindsImages::class)(path: getTestSupportPath('TestFiles'), only: [$format]))
            ->toHaveCount(1)
            ->toEqual([getTestFiles('test.'.$format)]);
    })->with(ImageFormat::toArray());

    it('fails with an exception when passing an invalid path')
        ->expect(fn () => app(FindsImages::class)(path: 'invalid', only: []))
        ->throws(RuntimeException::class, 'Path "invalid" does not exist');

    it('fails with an exception when no images found in an empty folder', function (): void {
        // arrange
        $tempDir = $this->tempDir->path();

        // act & assert
        expect(File::isEmptyDirectory($tempDir))->toBeTrue();

        expect(fn () => app(FindsImages::class)(path: getTempPath(), only: []))
            ->toThrow(FileNotFoundException::class);
    });
});
