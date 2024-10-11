<?php

declare(strict_types=1);

describe('Command Rules', function () {
    arch('HandlesValidation')
        ->expect('App\Commands')
        ->toUseTraits('App\Commands\Traits\HandlesValidation')
        ->ignoring('App\Commands\Traits');
});
