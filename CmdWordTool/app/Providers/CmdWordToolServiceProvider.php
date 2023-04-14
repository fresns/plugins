<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\CmdWordTool\Providers;

use Illuminate\Support\ServiceProvider;

class CmdWordToolServiceProvider extends ServiceProvider
{
    protected string $pluginName = 'CmdWordTool';

    protected string $pluginNameKebab = 'cmd-word-tool';

    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
