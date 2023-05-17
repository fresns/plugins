<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\WeChatLogin\Http\Controllers;

use App\Helpers\CacheHelper;
use App\Helpers\PluginHelper;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    public function index()
    {
        $version = PluginHelper::fresnsPluginVersionByFskey('WeChatLogin');

        $configKeys = [
            'wechatlogin_official_account',
            'wechatlogin_mini_program',
            'wechatlogin_open_platform',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        $officialAccount = $configs->where('item_key', 'wechatlogin_official_account')->first()?->item_value ?? [];
        $miniProgram = $configs->where('item_key', 'wechatlogin_mini_program')->first()?->item_value ?? [];
        $openPlatform = $configs->where('item_key', 'wechatlogin_open_platform')->first()?->item_value ?? [];

        return view('WeChatLogin::admin', compact('version', 'officialAccount', 'miniProgram', 'openPlatform'));
    }

    public function update(Request $request)
    {
        if ($request->officialAccount) {
            Config::updateOrCreate([
                'item_key' => 'wechatlogin_official_account',
            ],
                [
                    'item_value' => $request->officialAccount,
                    'item_type' => 'object',
                    'item_tag' => 'wechatlogin',
                ]);
        }

        if ($request->miniProgram) {
            Config::updateOrCreate([
                'item_key' => 'wechatlogin_mini_program',
            ],
                [
                    'item_value' => $request->miniProgram,
                    'item_type' => 'object',
                    'item_tag' => 'wechatlogin',
                ]);
        }

        if ($request->openPlatform) {
            Config::updateOrCreate([
                'item_key' => 'wechatlogin_open_platform',
            ],
                [
                    'item_value' => $request->openPlatform,
                    'item_type' => 'object',
                    'item_tag' => 'wechatlogin',
                ]);
        }

        CacheHelper::forgetFresnsConfigs([
            'wechatlogin_official_account',
            'wechatlogin_mini_program',
            'wechatlogin_open_platform',
        ]);

        return \response()->json([
            'code' => 0,
            'message' => 'success',
            'data' => null,
        ]);
    }
}
