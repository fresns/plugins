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

        $langTag = $request->langTag;
        if ($langTag) {
            $response = $next($request);

            $response->headers->set('X-Fresns-Client-Lang-Tag', $langTag);

            return $response;
        }

        return $next($request);
    }
}
