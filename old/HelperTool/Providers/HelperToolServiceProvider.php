<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\HelperTool\Providers;

use Illuminate\Support\ServiceProvider;

class HelperToolServiceProvider extends ServiceProvider
{
    protected string $pluginName = 'HelperTool';

    protected string $pluginNameKebab = 'helper-tool';

    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
