<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\NearbyDaysLimit\Controllers;

use App\Helpers\CacheHelper;
use App\Helpers\PluginHelper;
use App\Models\Config;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function index(Request $request)
    {
        $version = PluginHelper::fresnsPluginVersionByFskey('NearbyDaysLimit');

        $config = Config::where('item_key', 'nearby_days_limit')->first();

        $days = $config?->item_value ?? 7;

        return view('NearbyDaysLimit::index', compact('version', 'days'));
    }

    public function update(Request $request)
    {
        $days = $request->days;

        if (is_numeric($days)) {
            Config::withTrashed()->updateOrCreate([
                'item_key' => 'nearby_days_limit',
            ], [
                'item_value' => $days,
                'item_type' => 'number',
                'item_tag' => 'NearbyDaysLimit',
                'is_multilingual' => 0,
                'is_custom' => 0,
                'is_api' => 0,
                'deleted_at' => null,
            ]);

            CacheHelper::forgetFresnsConfigs('nearby_days_limit');
        }

        return $this->updateSuccess();
    }
}
