<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\OneTapDigest\Controllers;

use App\Fresns\Api\Traits\ApiResponseTrait;
use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\PrimaryHelper;
use App\Models\Comment;
use App\Models\PluginUsage;
use App\Models\Post;
use App\Utilities\ConfigUtility;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class WebController extends Controller
{
    use ApiResponseTrait;

    // index
    public function index(Request $request)
    {
        // Verify URL Authorization
        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->verifyUrlAuthorization([
            'urlAuthorization' => $request->authorization,
            'userLogin' => true,
        ]);

        $langTag = $fresnsResp->getData('langTag');
        View::share('langTag', $langTag);

        $code = $fresnsResp->getCode();
        $message = $fresnsResp->getMessage();
        $fsLang = ConfigHelper::fresnsConfigByItemKey('language_pack_contents', $langTag);
        $type = 'post';
        $primaryId = null;
        $authUlid = (string) Str::ulid();

        if ($fresnsResp->isErrorResponse()) {
            return view('OneTapDigest::index', compact('code', 'message', 'fsLang', 'type', 'primaryId', 'authUlid'));
        }

        if (! $request->pid && ! $request->cid) {
            $code = 30002;
            $message = ConfigUtility::getCodeMessage(30002, 'Fresns', $langTag);

            return view('OneTapDigest::index', compact('code', 'message', 'fsLang', 'type', 'primaryId', 'authUlid'));
        }

        // Judgment Source
        $errorCode = 37300;
        $groupId = null;

        if ($request->pid) {
            $groupId = PrimaryHelper::fresnsGroupIdByContentFsid('post', $request->pid);
        } else {
            $type = 'comment';
            $errorCode = 37400;

            $groupId = PrimaryHelper::fresnsGroupIdByContentFsid('comment', $request->cid);
        }

        $groupModel = PrimaryHelper::fresnsModelById('group', $groupId);

        // Verify the right to use
        $wordBody = [
            'fskey' => 'OneTapDigest',
            'type' => PluginUsage::TYPE_MANAGE,
            'uid' => $fresnsResp->getData('uid'),
            'gid' => $groupModel?->gid,
        ];
        $permResp = \FresnsCmdWord::plugin('Fresns')->checkExtendPerm($wordBody);

        if ($permResp->isErrorResponse()) {
            $code = $permResp->getCode();
            $message = $permResp->getMessage();

            return view('OneTapDigest::index', compact('code', 'message', 'fsLang', 'type', 'primaryId', 'authUlid'));
        }

        $model = match ($type) {
            'post' => Post::where('pid', $request->pid)->first(),
            'comment' => Comment::where('cid', $request->cid)->first(),
            default => null,
        };

        $primaryId = $model?->id;

        if (! $model) {
            $code = $errorCode;
            $message = ConfigUtility::getCodeMessage($errorCode, 'Fresns', $langTag);

            return view('OneTapDigest::index', compact('code', 'message', 'fsLang', 'type', 'primaryId', 'authUlid'));
        }

        if ($model->digest_state == Post::DIGEST_NO) {
            $model->update([
                'digest_state' => Post::DIGEST_GENERAL,
            ]);

            if ($type == 'post') {
                CacheHelper::clearDataCache('post', $model->pid);
            } else {
                CacheHelper::clearDataCache('comment', $model->cid);
            }

            $code = 0;
            $message = ConfigUtility::getCodeMessage(0, 'Fresns', $langTag);

            return view('OneTapDigest::index', compact('code', 'message', 'fsLang', 'type', 'primaryId', 'authUlid'));
        }

        $cacheTags = ['fresnsPlugins', 'fresnsPluginAuth'];
        CacheHelper::put($authUlid, $authUlid, $cacheTags, null, now()->addMinutes(10));

        $code = 10000;
        $message = ConfigUtility::getCodeMessage(0, 'Fresns', $langTag);

        return view('OneTapDigest::index', compact('code', 'message', 'fsLang', 'type', 'primaryId', 'authUlid'));
    }

    // update
    public function update(Request $request)
    {
        $langTag = $request->langTag;
        $cacheTags = ['fresnsPlugins', 'fresnsPluginAuth'];
        $authUlid = CacheHelper::get($request->authUlid, $cacheTags);

        if (! $request->authUlid || ! $authUlid) {
            $message = ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag);

            return $this->failure(32203, $message);
        }

        if (! $request->type || ! $request->primaryId) {
            $message = ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag);

            return $this->failure(30001, $message);
        }

        $model = match ($request->type) {
            'post' => Post::where('id', $request->primaryId)->first(),
            'comment' => Comment::where('id', $request->primaryId)->first(),
            default => null,
        };

        $errorCode = match ($request->type) {
            'post' => 37300,
            'comment' => 37400,
            default => 32201,
        };

        if (! $model) {
            $message = ConfigUtility::getCodeMessage($errorCode, 'Fresns', $langTag);

            return $this->failure($errorCode, $message);
        }

        $model->update([
            'digest_state' => Post::DIGEST_NO,
        ]);

        if ($request->type == 'post') {
            CacheHelper::clearDataCache('post', $model->pid);
        } else {
            CacheHelper::clearDataCache('comment', $model->cid);
        }

        return $this->success();
    }
}
