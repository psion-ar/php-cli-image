<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\FindImages;
use App\Actions\Forms\Components\SelectImages;
use App\Actions\SaveImage;
use App\Contracts\Actions\FindsImages;
use App\Contracts\Actions\Forms\Components\SelectsImages;
use App\Contracts\Actions\SavesImage;
use Illuminate\Support\ServiceProvider;

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
