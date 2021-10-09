<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\AqSms;

use Illuminate\Support\Facades\DB;

class AqSmsHelper
{
    // 新增或者更新值
    public static function insertOrUpdateConfigItem($itemKey, $itemValue = '')
    {
        $cond = [
            'item_key'   => $itemKey,
        ];
        $upInfo = [
            'item_value'   => $itemValue,
        ];
        DB::table('configs')->updateOrInsert($cond, $upInfo);
    }

    // 删除配置
    public static function deleteConfigItem($itemKey)
    {
        $cond = [
            'item_key'   => $itemKey,
        ];
        DB::table('configs')->where($cond)->delete();
    }

    // 新增
    public static function insertConfigs($itemKey, $itemValue = '', $itemType = 'string', $itemTag = 'aqsms')
    {
        $cond = [
            'item_key'   => $itemKey,
        ];
        $count = DB::table('configs')->where($cond)->count();
        if ($count > 0) {
            return;
        }
        $cond = [
            'item_key'   => $itemKey,
            'item_value'   => $itemValue,
            'item_type'   => $itemType,
            'item_tag'   => $itemTag,
        ];
        DB::table('configs')->insert($cond);
    }
}
