<?php

declare(strict_types=1);

use App\Commands\ResizeFitCommand;

describe('Argument validation', function () {
    test('path must be valid')
        ->artisan(ResizeFitCommand::class, ['path' => 'foo'])
        ->expectsOutputToContain('The path "foo" is not a valid file or directory path.')
        ->assertFailed();

    test('format must be valid')
        ->artisan(ResizeFitCommand::class, ['--format' => 'foo'])
        ->expectsOutputToContain('Format "foo" is not a valid image extension.')
        ->assertFailed();

    test('only must be valid')
        ->artisan(ResizeFitCommand::class, ['--only' => 'foo'])
        ->expectsOutputToContain('Only "foo" is not a valid image extension.')
        ->assertFailed();
});

describe('ResizeFitCommandTest', function () {
    beforeEach(fn () => $this->tempDir->empty());

    it('handles the image resize process')
        ->artisan(ResizeFitCommand::class, [
            'path' => getTestFiles(),
            '--target' => getTempPath(),
        ])
        ->expectsQuestion('Select the images to process', [getTestJpg()])
        ->expectsQuestion('Select the fit method', 'fill')
        ->expectsQuestion('Enter the width value', '400')
        ->expectsQuestion('Enter the height value', '400')
        ->expectsQuestion('Enter background color hex value', '#E89158')
        ->expectsOutputToContain('[Duration:]')
        ->assertSuccessful();
});
