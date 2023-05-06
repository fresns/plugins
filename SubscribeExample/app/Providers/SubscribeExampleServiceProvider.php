<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\SubscribeExample\Providers;

use Illuminate\Support\ServiceProvider;

class SubscribeExampleServiceProvider extends ServiceProvider
{
    protected string $pluginFskey = 'SubscribeExample';

    protected string $pluginFskeyKebab = 'subscribe-example';

    public function boot(): void
    {
        //
    }

    public function register(): void
    {
        //
    }
}
