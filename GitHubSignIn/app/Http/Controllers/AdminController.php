<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\GitHubSignIn\Http\Controllers;

use App\Helpers\CacheHelper;
use App\Helpers\PluginHelper;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    public function index()
    {
        $version = PluginHelper::fresnsPluginVersionByFskey('GitHubSignIn');

        $configKeys = [
            'github_signin_client_id',
            'github_signin_client_secret',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        $clientId = $configs->where('item_key', 'github_signin_client_id')->first()?->item_value ?? '';
        $clientSecret = $configs->where('item_key', 'github_signin_client_secret')->first()?->item_value ?? '';

        return view('GitHubSignIn::admin', compact('version', 'clientId', 'clientSecret'));
    }

    public function update(Request $request)
    {
        Config::updateOrCreate([
            'item_key' => 'github_signin_client_id',
        ], [
            'item_value' => $request->clientId,
            'item_type' => 'string',
        ]);

        Config::updateOrCreate([
            'item_key' => 'github_signin_client_secret',
        ], [
            'item_value' => $request->clientSecret,
            'item_type' => 'string',
        ]);

        CacheHelper::forgetFresnsConfigs([
            'github_signin_client_id',
            'github_signin_client_secret',
        ]);

        return back()->with('success', __('FsLang::tips.updateSuccess'));
    }
}
