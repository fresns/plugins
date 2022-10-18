<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\UtilityTool\Providers;

use Illuminate\Support\ServiceProvider;

class UtilityToolServiceProvider extends ServiceProvider
{
    protected string $pluginName = 'UtilityTool';

    protected string $pluginNameKebab = 'utility-tool';

    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
