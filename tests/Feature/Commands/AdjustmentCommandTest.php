<?php

declare(strict_types=1);

use App\Commands\AdjustmentCommand;

describe('AdjustmentCommandTest', function () {
    beforeEach(fn () => $this->tempDir->empty());

    it('handles the image adjustment process')
        ->artisan(AdjustmentCommand::class, [
            'path' => getTestFiles(),
            '--target' => getTempPath(),
        ])
        ->expectsQuestion('Select the images to process', [getTestJpg()])
        ->expectsQuestion('Select the adjustments to apply', ['brightness'])
        ->expectsQuestion('Enter the brightness value', '50')
        ->expectsOutputToContain('[Duration:]')
        ->assertSuccessful();
});
