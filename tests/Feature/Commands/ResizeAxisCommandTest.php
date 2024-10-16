<?php

declare(strict_types=1);

use Spatie\Image\Image;
use App\Commands\ResizeAxisCommand;

describe('Argument validation', function () {
    test('path must be valid')
        ->artisan(ResizeAxisCommand::class, ['path' => 'foo'])
        ->expectsOutputToContain('The path "foo" is not a valid file or directory path.')
        ->assertFailed();

    test('format must be valid')
        ->artisan(ResizeAxisCommand::class, ['--format' => 'foo'])
        ->expectsOutputToContain('Format "foo" is not a valid image extension.')
        ->assertFailed();

    test('only must be valid')
        ->artisan(ResizeAxisCommand::class, ['--only' => 'foo'])
        ->expectsOutputToContain('Only "foo" is not a valid image extension.')
        ->assertFailed();
});

describe('ResizeAxisCommandTest', function () {
    beforeEach(fn () => $this->tempDir->empty());

    it('resizes width', function () {
        // arrange
        copy(getTestJpg(), getTempPath('test.jpg'));

        // act & assert
        $actualWidth = Image::load(getTempPath('test.jpg'))->getWidth();
        expect($actualWidth)->toBe(340);

        // temp path contains only one file so prompt is skipped
        $this->artisan(ResizeAxisCommand::class, [ 'path' => getTempPath('test.jpg'), ])
            ->expectsQuestion('Select the axis to resize', 'width')
            ->expectsQuestion('Enter the Width value', '200')
            ->expectsOutputToContain('[Duration:]')
            ->assertSuccessful();

        $targetWidth = Image::load(getTempPath('test-1.jpg'))->getWidth();

        expect($targetWidth)->toBe(200);
    });

    it('resizes height', function () {
        // arrange
        copy(getTestJpg(), getTempPath('test.jpg'));

        // act & assert
        $actualHeight = Image::load(getTempPath('test.jpg'))->getHeight();
        expect($actualHeight)->toBe(280);

        // temp path contains only one file so prompt is skipped
        $this->artisan(ResizeAxisCommand::class, [ 'path' => getTempPath('test.jpg'), ])
            ->expectsQuestion('Select the axis to resize', 'height')
            ->expectsQuestion('Enter the Height value', '200')
            ->expectsOutputToContain('[Duration:]')
            ->assertSuccessful();

        $targetHeight = Image::load(getTempPath('test-1.jpg'))->getHeight();

        expect($targetHeight)->toBe(200);
    });

    it('preserves aspect ratio', function () {
        // arrange
        copy(getTestJpg(), getTempPath('test.jpg'));

        // act & assert
        $actualAspectRatio = (string) Image::load(getTempPath('test.jpg'))->getSize()->aspectRatio();

        $this->artisan(ResizeAxisCommand::class, [ 'path' => getTempPath('test.jpg'), ])
            ->expectsQuestion('Select the axis to resize', 'width')
            ->expectsQuestion('Enter the Width value', '200')
            ->expectsOutputToContain('[Duration:]')
            ->assertSuccessful();

        $targetAspectRatio = (string) Image::load(getTempPath('test-1.jpg'))->getSize()->aspectRatio();

        expect(bccomp($actualAspectRatio, $targetAspectRatio, 1))->toBe(0);
    });
});
