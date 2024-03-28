<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\AdminMenu\Http\Middleware;

use App\Helpers\AppHelper;
use App\Helpers\CacheHelper;
use App\Helpers\PrimaryHelper;
use App\Models\AppUsage;
use App\Utilities\ConfigUtility;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class CheckAccess
{
    public function handle(Request $request, Closure $next)
    {
        // verify access token
        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->verifyAccessToken([
            'accessToken' => $request->accessToken,
            'userLogin' => true,
        ]);

        $postMessageKey = $request->callbackKey;
        View::share('postMessageKey', $postMessageKey);

        $langTag = $fresnsResp->getData('langTag') ?? AppHelper::getLangTag();
        View::share('langTag', $langTag);

        if ($fresnsResp->isErrorResponse()) {
            $code = $fresnsResp->getCode();
            $message = $fresnsResp->getMessage();

            return response()->view('AdminMenu::tips', compact('code', 'message'), 403);
        }

        $type = 'user';
        if ($request->cid) {
            $type = 'comment';
        } elseif ($request->pid) {
            $type = 'post';
        }

        $primaryId = null;
        $groupId = null;

        switch ($type) {
            case 'post':
                $model = PrimaryHelper::fresnsModelByFsid('post', $request->pid);
                $groupId = $model?->group_id;

                $errorCode = 37400;
                break;

            case 'comment':
                $model = PrimaryHelper::fresnsModelByFsid('comment', $request->cid);
                $groupId = PrimaryHelper::fresnsGroupIdByContentFsid('comment', $request->cid);

                $errorCode = 37500;
                break;

            case 'user':
                $model = PrimaryHelper::fresnsModelByFsid('user', $request->uid);

                $errorCode = 31602;
                break;
        }

        if (empty($model)) {
            $code = $errorCode;
            $message = ConfigUtility::getCodeMessage($errorCode, 'Fresns', $langTag);

            return response()->view('AdminMenu::tips', compact('code', 'message'), 403);
        }

        $groupModel = PrimaryHelper::fresnsModelById('group', $groupId);

        // check extend perm
        $wordBody = [
            'fskey' => 'AdminMenu',
            'type' => AppUsage::TYPE_MANAGE,
            'uid' => $fresnsResp->getData('uid'),
            'gid' => $groupModel?->gid,
        ];
        $permResp = \FresnsCmdWord::plugin('Fresns')->checkExtendPerm($wordBody);

        if ($permResp->isErrorResponse()) {
            $code = $permResp->getCode();
            $message = $permResp->getMessage();

            return response()->view('AdminMenu::tips', compact('code', 'message'), 403);
        }

        // request attributes
        $request->attributes->add([
            'type' => $type,
            'langTag' => $langTag,
            'timezone' => $fresnsResp->getData('timezone'),
        ]);

        // plugin auth info
        $authUlid = (string) Str::ulid();

        CacheHelper::put('AdminMenu', $authUlid, 'fresnsPluginAuth', null, now()->addMinutes(15));

        $viewType = $request->viewType ?? 'list';

        Cookie::queue('fresns_plugin_admin_menu_auth_ulid', $authUlid);
        Cookie::queue('fresns_plugin_admin_menu_auth_uid', $fresnsResp->getData('uid'));
        Cookie::queue('fresns_plugin_admin_menu_lang_tag', $langTag);
        Cookie::queue('fresns_plugin_admin_menu_timezone', $fresnsResp->getData('timezone'));
        Cookie::queue('fresns_plugin_admin_menu_type', $type);
        Cookie::queue('fresns_plugin_admin_menu_primary_id', $model->id);
        Cookie::queue('fresns_plugin_admin_menu_view_type', $viewType);

        return $next($request);
    }
}
