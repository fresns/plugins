<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\UtilityTool\Providers;

use Illuminate\Support\ServiceProvider;

class UtilityToolServiceProvider extends ServiceProvider
{
    protected string $pluginFskey = 'UtilityTool';

    protected string $pluginFskeyKebab = 'utility-tool';

    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
