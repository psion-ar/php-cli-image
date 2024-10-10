<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\FindImages;
use App\Contracts\Actions\FindsImages;
use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        FindsImages::class => FindImages::class,
    ];
}
