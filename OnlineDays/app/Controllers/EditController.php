<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\OnlineDays\Controllers;

use App\Helpers\CacheHelper;
use App\Helpers\PluginHelper;
use App\Models\Config;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function index(Request $request)
    {
        $version = PluginHelper::fresnsPluginVersionByFskey('OnlineDays');

        $configs = Config::whereIn('item_key', [
            'extcredits1_name',
            'extcredits1_unit',
            'extcredits2_name',
            'extcredits2_unit',
            'extcredits3_name',
            'extcredits3_unit',
            'extcredits4_name',
            'extcredits4_unit',
            'extcredits5_name',
            'extcredits5_unit',
            'online_days_extcredits_id',
        ])->get();

        $extcredits1Name = $configs->where('item_key', 'extcredits1_name')->first()?->item_value ?? 'extcredits1';
        $extcredits1Unit = $configs->where('item_key', 'extcredits1_unit')->first()?->item_value ?? '';
        $extcredits2Name = $configs->where('item_key', 'extcredits2_name')->first()?->item_value ?? 'extcredits2';
        $extcredits2Unit = $configs->where('item_key', 'extcredits2_unit')->first()?->item_value ?? '';
        $extcredits3Name = $configs->where('item_key', 'extcredits3_name')->first()?->item_value ?? 'extcredits3';
        $extcredits3Unit = $configs->where('item_key', 'extcredits3_unit')->first()?->item_value ?? '';
        $extcredits4Name = $configs->where('item_key', 'extcredits4_name')->first()?->item_value ?? 'extcredits4';
        $extcredits4Unit = $configs->where('item_key', 'extcredits4_unit')->first()?->item_value ?? '';
        $extcredits5Name = $configs->where('item_key', 'extcredits5_name')->first()?->item_value ?? 'extcredits5';
        $extcredits5Unit = $configs->where('item_key', 'extcredits5_unit')->first()?->item_value ?? '';

        $extcreditsId = $configs->where('item_key', 'online_days_extcredits_id')->first()?->item_value ?? '';

        return view('OnlineDays::index', compact('version', 'extcreditsId', 'extcredits1Name', 'extcredits1Unit', 'extcredits2Name', 'extcredits2Unit', 'extcredits3Name', 'extcredits3Unit', 'extcredits4Name', 'extcredits4Unit', 'extcredits5Name', 'extcredits5Unit'));
    }

    public function update(Request $request)
    {
        Config::withTrashed()->updateOrCreate([
            'item_key' => 'online_days_extcredits_id',
        ], [
            'item_value' => $request->extcreditsId,
            'item_type' => 'number',
            'item_tag' => 'OnlineDays',
            'is_multilingual' => 0,
            'is_custom' => 0,
            'is_api' => 0,
            'deleted_at' => null,
        ]);

        CacheHelper::forgetFresnsConfigs('online_days_extcredits_id');

        return $this->updateSuccess();
    }
}
