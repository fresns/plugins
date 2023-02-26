<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasyManager\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CacheController extends Controller
{
    public function index(Request $request)
    {
        // search config
        $search = [
            'status' => false,
            'action' => null,
            'selects' => [],
            'defaultSelect' => [],
        ];

        // cache info
        $isSupportTags = Cache::supportsTags();
        $cacheDriver = Cache::getDefaultDriver();
        $cacheTagArr = Cache::get('fresns_cache_tags') ?? [];

        arsort($cacheTagArr);

        $cacheTags = [];
        foreach ($cacheTagArr as $tag => $datetime) {
            if ($request->type) {
                $isExist = Str::contains($tag, $request->type);

                if (! $isExist) {
                    continue;
                }
            }

            $item['tag'] = $tag;
            $item['name'] = Str::snake($tag, ' ');
            $item['datetime'] = $datetime;

            $cacheTags[] = $item;
        }

        return view('EasyManager::cache', compact('search', 'isSupportTags', 'cacheDriver', 'cacheTags'));
    }

    public function destroy(Request $request)
    {
        $isSupportTags = Cache::supportsTags();

        if (empty($request->tag) || ! $isSupportTags) {
            return;
        }

        $tags = (array) $request->tag;

        Cache::tags($tags)->flush();

        return $this->requestSuccess();
    }
}
