<?php

declare(strict_types=1);

namespace App\Commands;

use App\Actions\Forms\RenderResizeFitForm;
use App\Actions\Handler\HandleResizeFit;
use App\Commands\Traits\HandlesValidation;
use App\Contracts\Actions\FindsImages;
use App\DTOs\Request;
use App\Enums\ImageFormat;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

class ResizeFitCommand extends Command
{
    use HandlesValidation;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resize:fit
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
    protected $description = 'Resize an image using a certain fit method.';

    /**
     * Execute the console command.
     */
    public function handle(
        FindsImages $finder,
        RenderResizeFitForm $form,
        HandleResizeFit $handleResize,
    ): int {
        // Validate command arguments and options
        $validator = $this->validate();

        if ($validator->fails()) {
            collect($validator->errors()->all())->each(warning(...));

            return self::FAILURE;
        }

        ['path' => $path, 'format' => $format, 'only' => $only] = $validator->validated();

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

        return self::SUCCESS;
    }
}
