<?php

namespace Plugins\CmdWordTool\Providers;

use Illuminate\Support\ServiceProvider;

class CmdWordToolServiceProvider extends ServiceProvider
{
    /**
     * @var string $pluginName
     */
    protected string $pluginName = 'CmdWordTool';

    /**
     * @var string $pluginNameKebab
     */
    protected string $pluginNameKebab = 'cmd-word-tool';

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
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            plugin_path($this->pluginName, 'Config/config.php') => config_path($this->pluginNameKebab . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            plugin_path($this->pluginName, 'Config/config.php'), $this->pluginNameKebab
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/plugins/' . $this->pluginNameKebab);

        $sourcePath = plugin_path($this->pluginName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->pluginNameKebab . '-plugin-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->pluginNameKebab);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/plugins/' . $this->pluginNameKebab);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->pluginNameKebab);
        } else {
            $this->loadTranslationsFrom(plugin_path($this->pluginName, 'Resources/lang'), $this->pluginNameKebab);
        }
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

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/plugins/' . $this->pluginNameKebab)) {
                $paths[] = $path . '/plugins/' . $this->pluginNameKebab;
            }
        }
        return $paths;
    }
}
