<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EditorWorkspace\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }

    protected function mapWebRoutes()
    {
        Route::prefix('editor-workspace')->middleware('web')->group(dirname(__DIR__, 2).'/routes/web.php');
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api/editor-workspace')->group(dirname(__DIR__, 2).'/routes/api.php');
    }
}
