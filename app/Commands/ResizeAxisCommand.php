<?php

declare(strict_types=1);

namespace App\Commands;

use App\DTOs\Request;
use App\Enums\ImageFormat;
use App\Contracts\Actions\FindsImages;
use App\Commands\Traits\HandlesValidation;
use App\Actions\Forms\RenderResizeAxisForm;
use App\Actions\Handler\HandleResizeAxis;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

class ResizeAxisCommand extends Command
{
    use HandlesValidation;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resize:axis
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
    protected $description = 'Resize either the width or height of an image.';

    /**
     * Execute the console command.
     */
    public function handle(
        FindsImages $finder,
        RenderResizeAxisForm $form,
        HandleResizeAxis $handleResize,
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
            $metrics = $handleResize($request);

            // Display metrics
            info($metrics->get());
        } catch (\Exception $e) {
            error($error = $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
