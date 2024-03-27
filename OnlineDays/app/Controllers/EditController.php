<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\OnlineDays\Controllers;

use App\Helpers\CacheHelper;
use App\Helpers\PluginHelper;
use App\Helpers\StrHelper;
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

        $extcredits1NameArr = $configs->where('item_key', 'extcredits1_name')->first()?->item_value;
        $extcredits1Name = StrHelper::languageContent($extcredits1NameArr) ?? 'extcredits1';

        $extcredits1UnitArr = $configs->where('item_key', 'extcredits1_unit')->first()?->item_value;
        $extcredits1Unit = StrHelper::languageContent($extcredits1UnitArr);

        $extcredits2NameArr = $configs->where('item_key', 'extcredits2_name')->first()?->item_value;
        $extcredits2Name = StrHelper::languageContent($extcredits2NameArr) ?? 'extcredits2';

        $extcredits2UnitArr = $configs->where('item_key', 'extcredits2_unit')->first()?->item_value;
        $extcredits2Unit = StrHelper::languageContent($extcredits2UnitArr);

        $extcredits3NameArr = $configs->where('item_key', 'extcredits3_name')->first()?->item_value;
        $extcredits3Name = StrHelper::languageContent($extcredits3NameArr) ?? 'extcredits3';

        $extcredits3UnitArr = $configs->where('item_key', 'extcredits3_unit')->first()?->item_value;
        $extcredits3Unit = StrHelper::languageContent($extcredits3UnitArr);

        $extcredits4NameArr = $configs->where('item_key', 'extcredits4_name')->first()?->item_value;
        $extcredits4Name = StrHelper::languageContent($extcredits4NameArr) ?? 'extcredits4';

        $extcredits4UnitArr = $configs->where('item_key', 'extcredits4_unit')->first()?->item_value;
        $extcredits4Unit = StrHelper::languageContent($extcredits4UnitArr);

        $extcredits5NameArr = $configs->where('item_key', 'extcredits5_name')->first()?->item_value;
        $extcredits5Name = StrHelper::languageContent($extcredits5NameArr) ?? 'extcredits5';

        $extcredits5UnitArr = $configs->where('item_key', 'extcredits5_unit')->first()?->item_value;
        $extcredits5Unit = StrHelper::languageContent($extcredits5UnitArr);

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
            'is_multilingual' => 0,
            'is_custom' => 1,
            'is_api' => 0,
            'deleted_at' => null,
        ]);

        CacheHelper::forgetFresnsConfigs('online_days_extcredits_id');

        return $this->updateSuccess();
    }
}
