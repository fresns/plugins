<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Plugins\FresnsEmail\Controllers;

use App\Base\Controllers\BaseController;
use App\Http\Center\Helper\CmdRpcHelper;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigs;
use App\Plugins\FresnsEmail\Plugin;
use App\Plugins\FresnsEmail\PluginConfig;
use Illuminate\Http\Request;

class WebController extends BaseController
{
    /**
     *  mail setting.
     */
    public function settings()
    {
        $content = FresnsConfigs::query()->whereIn('item_key', [
            'fresnsemail_smtp_host',
            'fresnsemail_smtp_port',
            'fresnsemail_smtp_user',
            'fresnsemail_smtp_password',
            'fresnsemail_verify_type',
            'fresnsemail_from_mail',
            'fresnsemail_from_name',
        ])->pluck('item_value', 'item_key');

        return view('plugins.FresnsEmail.setting', compact('content'));
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Throwable
     */
    public function postSettings(Request $request)
    {
        collect($request->post())->each(function (?string $value, string $key) {
            $fresnsConfigs = FresnsConfigs::query()->firstWhere('item_key', $key) ?: FresnsConfigs::query()->newModelInstance();
            $fresnsConfigs->item_key = $key;
            $fresnsConfigs->item_value = $value ?: '';
            $fresnsConfigs->item_type = 'string';
            $fresnsConfigs->item_tag = 'fresnsemail';
            $fresnsConfigs->saveOrFail();
        });
        return back()->with('success', __('success!'));
    }

    /**
     * test send.
     */
    public function sendTest(Request $request)
    {
        $email = $request->input('email');
        $input = [
            'email' => $email,
            'title' => 'Fresns test email',
            'content' => 'This is a Fresns software testing email',
        ];
        $resp = CmdRpcHelper::call(Plugin::class, PluginConfig::PLG_CMD_SEND_EMAIL, $input);
        if (CmdRpcHelper::isErrorCmdResp($resp)) {
			return response()->json(['code'=>'500000'], Response::HTTP_OK);
        }
	    return response()->json(['code'=>'000000'], Response::HTTP_OK);
    }
}
