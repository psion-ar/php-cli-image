<?php

declare(strict_types=1);

use App\Commands\EffectCommand;
use Illuminate\Support\Facades\File;

describe('Argument validation', function () {
    test('path must be valid')
        ->artisan(EffectCommand::class, ['path' => 'foo'])
        ->expectsOutputToContain('The path "foo" is not a valid file or directory path.')
        ->assertFailed();

    test('format must be valid')
        ->artisan(EffectCommand::class, ['--format' => 'foo'])
        ->expectsOutputToContain('Format "foo" is not a valid image extension.')
        ->assertFailed();

    test('only must be valid')
        ->artisan(EffectCommand::class, ['--only' => 'foo'])
        ->expectsOutputToContain('Only "foo" is not a valid image extension.')
        ->assertFailed();
});

describe('EffectCommand', function () {
    beforeEach(fn () => $this->tempDir->empty());

    it('handles the image manipulation process')
        ->artisan(EffectCommand::class, [
            'path' => getTestFiles(),
            '--target' => getTempPath(),
        ])
        ->expectsQuestion('Select the images to process', [getTestJpg()])
        ->expectsQuestion('Select the effects to apply', ['blur'])
        ->expectsQuestion('Enter the Blur effect value', '50')
        ->expectsOutputToContain('[Duration:]')
        ->assertSuccessful();

    it('exits with error when no images are found', function (): void {
        // arrange
        $tempDir = $this->tempDir->path();

        // act & assert
        expect(File::isEmptyDirectory($tempDir))->toBeTrue();

        $this->artisan(EffectCommand::class, ['path' => $tempDir])
            ->expectsOutputToContain('No images found')
            ->assertFailed();
    });
});
