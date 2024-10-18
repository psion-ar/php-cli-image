<?php

declare(strict_types=1);

namespace App\Commands;

use App\Actions\Forms\RenderColorizeForm;
use App\Actions\Handler\HandleColorize;
use App\Commands\Traits\HandlesValidation;
use App\Contracts\Actions\FindsImages;
use App\DTOs\Request;
use App\Enums\ImageFormat;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

class ColorizeCommand extends Command
{
    use HandlesValidation;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'colorize
                            {path? : The source file or directory path. Defaults to current working directory if omitted.}
                            {--t|target= : The target directory path.}
                            {--a|all : Skip interactive file selection prompt. Selects all files.}
                            {--f|format= : The image format to convert to.}
                            {--o|only=* : Only files with the given extension(s).}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Colorize images.';

    /**
     * Execute the console command.
     */
    public function handle(
        FindsImages $finder,
        RenderColorizeForm $form,
        HandleColorize $handleColorize
    ): int {
        // Validate command arguments and options
        $validator = $this->validate();

        if ($validator->fails()) {
            collect($validator->errors()->all())->each(warning(...));

            return self::FAILURE;
        }

        ['path' => $path, 'format' => $format, 'only' => $only] = $validator->validated();

        try {
            // Find images
            $paths = $finder($path, $only);

            // Render form
            $response = $form($paths, $this->option('all'));

            $request = Request::create([
                'command' => $this->argument('command'),
                'target' => $this->option('target'),
                'format' => $format ? ImageFormat::from($format) : null,
                ...$response,
            ]);

            // Handle effects
            $metrics = $handleColorize($request);

            // Display metrics
            info($metrics->get());
        } catch (\Exception $e) {
            error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
