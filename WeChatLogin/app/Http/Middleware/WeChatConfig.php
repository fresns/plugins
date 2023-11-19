<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\WeChatLogin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Plugins\WeChatLogin\Helpers\LoginHelper;

class WeChatConfig
{
    public function handle(Request $request, Closure $next)
    {
        $isWeChat = LoginHelper::isWeChat();
        View::share('isWeChat', $isWeChat);

        $response = $next($request);

        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response;
        }

        $langTag = $request->langTag;
        if ($langTag && $response->headers) {
            $response->headers->set('X-Fresns-Client-Lang-Tag', $langTag);
        }

        return $response;
    }
}
