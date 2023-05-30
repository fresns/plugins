<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\TitleIcons\Controllers;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\PrimaryHelper;
use App\Models\Comment;
use App\Models\Operation;
use App\Models\OperationUsage;
use App\Models\PluginUsage;
use App\Models\Post;
use App\Utilities\ConfigUtility;
use App\Utilities\PermissionUtility;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function index(Request $request)
    {
        // Validate path credentials
        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->verifyUrlAuthorization([
            'urlAuthorization' => $request->authorization,
            'userLogin' => true,
        ]);

        $langTag = $fresnsResp->getData('langTag');
        View::share('langTag', $langTag);

        if ($fresnsResp->isErrorResponse()) {
            return view('TitleIcons::error', [
                'code' => $fresnsResp->getCode(),
                'message' => $fresnsResp->getMessage(),
            ]);
        }

        // Determining mandatory parameters
        if (! $request->scene) {
            return view('TitleIcons::error', [
                'code' => 30001,
                'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        // Source of judgement
        if ($request->cid) {
            $type = 'comment';
            $model = PrimaryHelper::fresnsModelByFsid('comment', $request->cid);

            $groupId = PrimaryHelper::fresnsGroupIdByContentFsid('comment', $request->cid);
        } elseif ($request->pid) {
            $type = 'post';
            $model = PrimaryHelper::fresnsModelByFsid('post', $request->pid);

            $groupId = PrimaryHelper::fresnsGroupIdByContentFsid('post', $request->pid);
        } else {
            return view('TitleIcons::error', [
                'code' => 30002,
                'message' => ConfigUtility::getCodeMessage(30002, 'Fresns', $langTag),
            ]);
        }

        // Verify entitlement to use
        $userId = PrimaryHelper::fresnsUserIdByUidOrUsername($fresnsResp->getData('uid'));
        $checkPerm = PermissionUtility::checkExtendPerm('TitleIcons', PluginUsage::TYPE_MANAGE, $groupId, $userId);
        if (! $checkPerm) {
            return view('TitleIcons::error', [
                'code' => 35301,
                'message' => ConfigUtility::getCodeMessage(35301, 'Fresns', $langTag),
            ]);
        }

        $fsLang = ConfigHelper::fresnsConfigByItemKey('language_pack_contents', $langTag);

        $authUlid = (string) Str::ulid();
        $cacheTags = ['fresnsPlugins', 'pluginTitleIcons', 'fresnsPluginAuth'];

        CacheHelper::put($authUlid, $authUlid, $cacheTags, null, now()->addMinutes(10));

        $titles = Operation::where('type', 3)->where('code', 'title')->get();

        return view('TitleIcons::index', compact('type', 'model', 'titles', 'authUlid', 'langTag', 'fsLang'));
    }

    // edit post title icon
    public function editPostTitleIcon(Request $request)
    {
        $langTag = $request->langTag ?? ConfigHelper::fresnsConfigDefaultLangTag();
        View::share('langTag', $langTag);

        if (! $request->authUlid || ! $request->pid) {
            return view('AdminMenu::error', [
                'code' => 30001,
                'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        $cacheTags = ['fresnsPlugins', 'pluginTitleIcons', 'fresnsPluginAuth'];
        $authUlid = CacheHelper::get($request->authUlid, $cacheTags);

        if (empty($authUlid)) {
            return view('AdminMenu::error', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
            ]);
        }

        $post = Post::where('pid', $request->pid)->first();

        if (empty($post)) {
            return view('AdminMenu::error', [
                'code' => 37300,
                'message' => ConfigUtility::getCodeMessage(37300, 'Fresns', $langTag),
            ]);
        }

        $titles = Operation::where('type', 3)->where('code', 'title')->get();

        foreach ($titles as $title) {
            $operation = OperationUsage::where('usage_type', OperationUsage::TYPE_POST)
                ->where('usage_id', $post->id)
                ->where('operation_id', $title->id)
                ->first();

            if (! $operation) {
                continue;
            }

            $operation->delete();
        }

        if ($request->operationId) {
            OperationUsage::create([
                'usage_type' => OperationUsage::TYPE_POST,
                'usage_id' => $post->id,
                'operation_id' => $request->operationId,
            ]);
        }

        CacheHelper::clearDataCache('post', $request->pid, 'fresnsApiData');

        return view('AdminMenu::error', [
            'code' => 0,
            'message' => ConfigUtility::getCodeMessage(0, 'Fresns', $langTag),
        ]);
    }

    // edit comment title icon
    public function editCommentTitleIcon(Request $request)
    {
        $langTag = $request->langTag ?? ConfigHelper::fresnsConfigDefaultLangTag();
        View::share('langTag', $langTag);

        if (! $request->authUlid || ! $request->cid) {
            return view('AdminMenu::error', [
                'code' => 30001,
                'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        $cacheTags = ['fresnsPlugins', 'pluginTitleIcons', 'fresnsPluginAuth'];
        $authUlid = CacheHelper::get($request->authUlid, $cacheTags);

        if (empty($authUlid)) {
            return view('AdminMenu::error', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
            ]);
        }

        $comment = Comment::where('cid', $request->cid)->first();

        if (empty($comment)) {
            return view('AdminMenu::error', [
                'code' => 37300,
                'message' => ConfigUtility::getCodeMessage(37300, 'Fresns', $langTag),
            ]);
        }

        $titles = Operation::where('type', 3)->where('code', 'title')->get();

        foreach ($titles as $title) {
            $operation = OperationUsage::where('usage_type', OperationUsage::TYPE_COMMENT)
                ->where('usage_id', $comment->id)
                ->where('operation_id', $title->id)
                ->first();

            if (! $operation) {
                continue;
            }

            $operation->delete();
        }

        if ($request->operationId) {
            OperationUsage::create([
                'usage_type' => OperationUsage::TYPE_COMMENT,
                'usage_id' => $comment->id,
                'operation_id' => $request->operationId,
            ]);
        }

        CacheHelper::clearDataCache('comment', $request->cid, 'fresnsApiData');

        return view('AdminMenu::error', [
            'code' => 0,
            'message' => ConfigUtility::getCodeMessage(0, 'Fresns', $langTag),
        ]);
    }
}
