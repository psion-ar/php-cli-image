<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\SaveImage;
use App\Actions\FindImages;
use App\Contracts\Actions\SavesImage;
use App\Contracts\Actions\FindsImages;
use Illuminate\Support\ServiceProvider;
use App\Actions\Forms\Components\SelectImages;
use App\Contracts\Actions\Forms\Components\SelectsImages;

class ActionServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        FindsImages::class => FindImages::class,
        SavesImage::class => SaveImage::class,
        SelectsImages::class => SelectImages::class,
    ];
}
