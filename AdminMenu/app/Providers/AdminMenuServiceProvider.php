<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\AdminMenu\Providers;

use Illuminate\Support\ServiceProvider;

class AdminMenuServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerViews();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadTranslationsFrom(dirname(__DIR__, 2).'/resources/lang', 'AdminMenu');
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $this->loadViewsFrom(dirname(__DIR__, 2).'/resources/views', 'AdminMenu');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
