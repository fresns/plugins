<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class QiNiuServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        Paginator::useBootstrap();
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerConfig();
        $this->registerViews();
    }

    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/filesystems.php', 'fresns-qiniu-filesystems'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    protected function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'QiNiu');
    }
}
