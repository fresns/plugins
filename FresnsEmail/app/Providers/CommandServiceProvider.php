<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEmail\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $commandsDirectory = dirname(__DIR__).'/Console/Commands';
        if (File::exists($commandsDirectory)) {
            $this->load($commandsDirectory);
        }
    }

    /**
     * Register all of the commands in the given directory.
     *
     * @param  array|string  $paths
     * @return void
     */
    protected function load($paths)
    {
        $paths = array_unique(Arr::wrap($paths));

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return;
        }

        $commands = [];
        foreach ((new Finder)->in($paths)->files() as $command) {
            $commands[] = Str::before(self::class, 'Providers\\')
                .'Console\\Commands\\'.str_replace('.php', '', $command->getBasename());
        }
        $this->commands($commands);
    }
}
