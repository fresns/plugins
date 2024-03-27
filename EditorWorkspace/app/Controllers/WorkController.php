<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EditorWorkspace\Controllers;

use App\Fresns\Api\Http\Controllers\GroupController;
use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\FileHelper;
use App\Models\Account;
use App\Models\AppUsage;
use App\Models\Config;
use App\Models\File;
use App\Models\FileUsage;
use App\Models\Post;
use App\Utilities\ConfigUtility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class WorkController extends Controller
{
    public function index(Request $request)
    {
        // check access token
        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->verifyAccessToken([
            'accessToken' => $request->accessToken,
            'userLogin' => true,
        ]);

        if ($fresnsResp->isErrorResponse()) {
            return view('EditorWorkspace::error', [
                'code' => $fresnsResp->getCode(),
                'message' => $fresnsResp->getMessage(),
            ]);
        }

        $headers = $fresnsResp->getData();

        // verify the right to use
        $wordBody = [
            'fskey' => 'EditorWorkspace',
            'type' => AppUsage::TYPE_FEATURE,
            'uid' => $headers['uid'],
        ];
        $permResp = \FresnsCmdWord::plugin('Fresns')->checkExtendPerm($wordBody);

        if ($permResp->isErrorResponse()) {
            return view('EditorWorkspace::tips', [
                'code' => $permResp->getCode(),
                'message' => $permResp->getMessage(),
            ]);
        }

        $authUlid = (string) Str::ulid();
        $cacheTags = ['fresnsPlugins', 'pluginEditorWorkspace', 'fresnsPluginAuth'];

        CacheHelper::put($headers, $authUlid, $cacheTags, null, now()->addHours(2)); // valid for 2 hours

        return redirect()->to(route('editor-workspace.work.editor', [
            'authUlid' => $authUlid,
        ]));
    }

    public function editor(Request $request)
    {
        $authUlid = $request->authUlid;

        if (! $authUlid) {
            return view('EditorWorkspace::error', [
                'code' => 35301,
                'message' => ConfigUtility::getCodeMessage(35301),
            ]);
        }

        $cacheTags = ['fresnsPlugins', 'pluginEditorWorkspace', 'fresnsPluginAuth'];
        $headers = CacheHelper::get($authUlid, $cacheTags);

        if (! $headers) {
            return view('EditorWorkspace::error', [
                'code' => 35301,
                'message' => ConfigUtility::getCodeMessage(35301),
            ]);
        }

        $fsConfigs = ConfigHelper::fresnsConfigByItemKeys([
            'post_editor_group',
            'post_editor_group_required',
            'post_editor_title',
            'post_editor_title_view',
            'post_editor_title_required',
            'post_editor_title_length',
            'post_editor_image',
            'group_name',
            'publish_post_name',
        ]);
        $fileAccept = FileHelper::fresnsFileAcceptByType();
        $fsLang = ConfigHelper::fresnsConfigLanguagePack($headers['langTag']);

        $groupCategories = static::groupCategories();

        // users
        $accountConfig = Config::where('item_key', 'editor_workspace_accounts')->first();

        $accountIds = $accountConfig?->item_value ?? [];

        $accounts = [];
        if ($accountIds) {
            $accounts = Account::with(['users'])->whereIn('id', $accountIds)->get();
        }

        $users = collect();

        foreach ($accounts as $account) {
            $users = $users->merge($account->users);
        }

        return view('EditorWorkspace::work.editor', compact('authUlid', 'headers', 'fsConfigs', 'fileAccept', 'fsLang', 'groupCategories', 'users'));
    }

    public static function groupCategories(): array
    {
        $query = [
            'topGroups' => 1,
            'pageSize' => 100,
            'page' => 1,
        ];

        $request = Request::create('/api/fresns/v1/group/list', 'GET', $query);

        $apiController = new GroupController();
        $response = $apiController->categories($request);

        $resultContent = $response->getContent();
        $result = json_decode($resultContent, true);

        return data_get($result, 'data.list', []) ?? [];
    }

    public function groups(Request $request, string $gid): JsonResponse
    {
        $uid = $request->uid;

        $query = [
            'gid' => $gid,
            'pageSize' => $request->pageSize ?? 20,
            'page' => $request->page ?? 1,
        ];

        $internalRequest = Request::create('/api/fresns/v1/group/list', 'GET', $query);

        $request->headers->set('X-Fresns-Uid', $uid);

        $apiController = new GroupController();
        $response = $apiController->list($internalRequest);

        $resultContent = $response->getContent();
        $result = json_decode($resultContent, true);

        return Response::json(data_get($result, 'data', []) ?? []);
    }

    public function quickPublish(Request $request)
    {
        $result = [
            'code' => 0,
            'message' => 'ok',
            'data' => null,
        ];

        // check authUlid
        $authUlid = $request->authUlid;
        if (! $authUlid) {
            $result['code'] = 35301;
            $result['message'] = ConfigUtility::getCodeMessage(35301);

            return Response::json($result);
        }

        $cacheTags = ['fresnsPlugins', 'pluginEditorWorkspace', 'fresnsPluginAuth'];
        $headers = CacheHelper::get($authUlid, $cacheTags);

        if (! $headers) {
            $result['code'] = 35301;
            $result['message'] = ConfigUtility::getCodeMessage(35301);

            return Response::json($result);
        }

        // check uid
        $uid = $request->uid;
        if (! $uid) {
            $result['code'] = 31602;
            $result['message'] = ConfigUtility::getCodeMessage(31602);

            return Response::json($result);
        }

        $wordBody = [
            'uid' => $uid,
            'type' => 1,
            'postQuotePid' => $request->postQuotePid,
            'postGid' => $request->postGid,
            'postTitle' => $request->postTitle,
            'postIsCommentDisabled' => $request->postIsCommentDisabled,
            'postIsCommentPrivate' => $request->postIsCommentPrivate,
            'content' => $request->content,
            'isMarkdown' => $request->isMarkdown,
            'map' => $request->map,
            'extends' => $request->extends,
            'archives' => $request->archives,
            'requireReview' => false,
        ];

        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->contentQuickPublish($wordBody);

        if ($fresnsResp->isErrorResponse()) {
            return $fresnsResp->getErrorResponse();
        }

        // upload file
        if ($request->image) {
            $fileWordBody = [
                'usageType' => FileUsage::TYPE_POST,
                'platformId' => 4,
                'tableName' => 'posts',
                'tableColumn' => 'id',
                'tableId' => $fresnsResp->getData('id'),
                'tableKey' => null,
                'aid' => null,
                'uid' => $uid,
                'type' => File::TYPE_IMAGE,
                'moreJson' => null,
                'file' => $request->image,
            ];

            \FresnsCmdWord::plugin('Fresns')->uploadFile($fileWordBody);
        }

        if ($request->datetime) {
            $post = Post::where('id', $fresnsResp->getData('id'))->first();

            $post?->update([
                'created_at' => $request->datetime,
            ]);
        }

        return Response::json($result);
    }
}
