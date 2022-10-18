<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class QiNiuServiceProvider extends BaseServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();

        $this->loadMigrationsFrom(dirname(__DIR__, 2).'/database/migrations');

        // Event::listen(UserCreated::class, UserCreatedListener::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        if ($this->app->runningInConsole()) {
            $this->app->register(CommandServiceProvider::class);
        }
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__, 2).'/config/qiniu.php', 'qiniu'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__, 2).'/config/filesystems.php', 'fresns-qiniu-filesystems'
        );

        $this->publishes([
            __DIR__.'/../../config/qiniu.php' => config_path('qiniu.php'),
        ], 'config');
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $this->loadViewsFrom(dirname(__DIR__, 2).'/resources/views', 'QiNiu');

        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/plugins/qiniu'),
        ], ['views', 'qiniu-plugin-views']);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->loadTranslationsFrom(dirname(__DIR__, 2).'/resources/lang', 'QiNiu');
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
