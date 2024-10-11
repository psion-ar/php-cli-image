<?php

declare(strict_types=1);

namespace App\Commands\Traits;

use App\Rules\FileOrDirectory;
use App\Rules\ImageExtension;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;

trait HandlesValidation
{
    /**
     * Validate the command's input
     */
    public function validate(): Validator
    {
        return ValidatorFacade::make(
            [
                'path' => $this->argument('path'),
                'format' => $this->option('format'),
                'only' => $this->option('only'),
            ],
            [
                'path' => ['nullable', 'string', new FileOrDirectory],
                'format' => ['nullable', 'string', new ImageExtension],
                'only' => ['array', 'nullable', new ImageExtension],
            ],
        );
    }
}
