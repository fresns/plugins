<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\AdminMenu\Http\Middleware;

use App\Fresns\Api\Exceptions\ResponseException;
use App\Helpers\CacheHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CheckAuth
{
    public function handle(Request $request, Closure $next)
    {
        $authUlid = Cookie::get('fresns_plugin_admin_menu_auth_ulid');
        if (empty($authUlid)) {
            throw new ResponseException(30001);
        }

        $cacheAuthUlid = CacheHelper::get($authUlid, 'fresnsPluginAuth');
        if (empty($cacheAuthUlid)) {
            throw new ResponseException(32203);
        }

        $primaryId = Cookie::get('fresns_plugin_admin_menu_primary_id');
        if (empty($primaryId)) {
            throw new ResponseException(30001);
        }

        return $next($request);
    }
}
