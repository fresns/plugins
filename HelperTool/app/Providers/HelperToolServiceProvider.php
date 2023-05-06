<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\HelperTool\Providers;

use Illuminate\Support\ServiceProvider;

class HelperToolServiceProvider extends ServiceProvider
{
    protected string $pluginFskey = 'HelperTool';

    protected string $pluginFskeyKebab = 'helper-tool';

    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
