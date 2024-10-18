<?php

declare(strict_types=1);

use App\Commands\ColorizeCommand;

describe('ColorizeCommandTest', function () {
    beforeEach(fn () => $this->tempDir->empty());

    it('handles the image colorize process')
        ->artisan(ColorizeCommand::class, [
            'path' => getTestFiles(),
            '--target' => getTempPath(),
        ])
        ->expectsQuestion('Select the images to process', [getTestJpg()])
        ->expectsQuestion('Enter the red value', '50')
        ->expectsQuestion('Enter the green value', '50')
        ->expectsQuestion('Enter the blue value', '50')
        ->expectsOutputToContain('[Duration:]')
        ->assertSuccessful();
});
