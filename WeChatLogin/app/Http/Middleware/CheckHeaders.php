<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\WeChatLogin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->checkHeaders();

        if ($fresnsResp->isErrorResponse()) {
            return $fresnsResp->errorResponse();
        }

        return $next($request);
    }
}
