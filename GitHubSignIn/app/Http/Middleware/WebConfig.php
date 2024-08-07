<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\GitHubSignIn\Http\Middleware;

use App\Helpers\AppHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class WebConfig
{
    public function handle(Request $request, Closure $next)
    {
        $langTag = $request->langTag ?? AppHelper::getLangTag();

        View::share('langTag', $langTag);

        $request->headers->set('X-Fresns-Client-Lang-Tag', $langTag);

        return $next($request);
    }
}
