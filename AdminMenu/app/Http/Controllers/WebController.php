<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\AdminMenu\Http\Controllers;

use App\Fresns\Api\Exceptions\ResponseException;
use App\Fresns\Api\Http\Controllers\GroupController;
use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\PrimaryHelper;
use App\Helpers\StrHelper;
use App\Models\Account;
use App\Models\Comment;
use App\Models\CommentLog;
use App\Models\Group;
use App\Models\Post;
use App\Models\PostLog;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Utilities\ConfigUtility;
use App\Utilities\DetailUtility;
use App\Utilities\InteractionUtility;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;

class WebController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->attributes->get('type');
        $langTag = $request->attributes->get('langTag');
        $timezone = $request->attributes->get('timezone');

        $detail = [];
        $roles = [];

        switch ($type) {
            case 'post':
                $model = PrimaryHelper::fresnsModelByFsid('post', $request->pid);

                $detail = DetailUtility::postDetail($model, $langTag, $timezone);
                break;

            case 'comment':
                $model = PrimaryHelper::fresnsModelByFsid('comment', $request->cid);

                $detail = DetailUtility::commentDetail($model, $langTag, $timezone);
                break;

            case 'user':
                $model = PrimaryHelper::fresnsModelByFsid('user', $request->uid);

                $detail = DetailUtility::userDetail($model, $langTag, $timezone);

                // get roles
                $roleQuery = Role::orderBy('sort_order')->get();

                $roleList = [];
                foreach ($roleQuery as $role) {
                    if (! $role->is_enabled) {
                        continue;
                    }

                    $item['rid'] = $role->rid;
                    $item['name'] = StrHelper::languageContent($role->name, $langTag);

                    $roleList[] = $item;
                }
                $roles = $roleList;
                break;
        }

        $fsLang = ConfigHelper::fresnsConfigLanguagePack($langTag);
        $fsName = ConfigHelper::fresnsConfigByItemKeys([
            'user_name',
            'user_uid_name',
            'user_username_name',
            'user_nickname_name',
            'user_role_name',
            'group_name',
        ], $langTag);

        return view('AdminMenu::index', compact('type', 'detail', 'roles', 'fsLang', 'fsName'));
    }

    // groups
    public function groups(Request $request)
    {
        $authUid = Cookie::get('fresns_plugin_admin_menu_auth_uid');

        $request->headers->set('X-Fresns-Uid', $authUid);

        try {
            $request = Request::create('/api/fresns/v1/group/list', 'GET', $request->all());

            $apiController = new GroupController();
            $response = $apiController->list($request);

            $resultContent = $response->getContent();
            $result = json_decode($resultContent, true);
        } catch (\Exception $e) {
            $code = (int) $e->getCode();

            throw new ResponseException($code);
        }

        return response()->json($result);
    }

    // delete post
    public function deletePost()
    {
        $langTag = Cookie::get('fresns_plugin_admin_menu_lang_tag');
        $type = Cookie::get('fresns_plugin_admin_menu_type');

        if ($type != 'post') {
            return response()->json([
                'code' => 30002,
                'message' => ConfigUtility::getCodeMessage(30002, 'Fresns', $langTag),
                'data' => [],
            ]);
        }

        $primaryId = Cookie::get('fresns_plugin_admin_menu_primary_id');
        $post = Post::where('id', $primaryId)->first();

        if (empty($post)) {
            return response()->json([
                'code' => 37400,
                'message' => ConfigUtility::getCodeMessage(37400, 'Fresns', $langTag),
                'data' => [],
            ]);
        }

        $detail = DetailUtility::postDetail($post, $langTag);

        InteractionUtility::publishStats('post', $post->id, 'decrement');

        PostLog::where('post_id', $post->id)->delete();

        CacheHelper::clearDataCache('post', $post->pid);

        $post->delete();

        return response()->json([
            'code' => 0,
            'message' => ConfigUtility::getCodeMessage(0, 'Fresns', $langTag),
            'data' => $detail,
        ]);
    }

    // delete comment
    public function deleteComment()
    {
        $langTag = Cookie::get('fresns_plugin_admin_menu_lang_tag');
        $type = Cookie::get('fresns_plugin_admin_menu_type');

        if ($type != 'comment') {
            return response()->json([
                'code' => 30002,
                'message' => ConfigUtility::getCodeMessage(30002, 'Fresns', $langTag),
                'data' => [],
            ]);
        }

        $primaryId = Cookie::get('fresns_plugin_admin_menu_primary_id');
        $comment = Comment::where('id', $primaryId)->first();

        if (empty($comment)) {
            return response()->json([
                'code' => 37500,
                'message' => ConfigUtility::getCodeMessage(37500, 'Fresns', $langTag),
                'data' => [],
            ]);
        }

        $detail = DetailUtility::commentDetail($comment, $langTag);

        InteractionUtility::publishStats('comment', $comment->id, 'decrement');

        CommentLog::where('comment_id', $comment->id)->delete();

        CacheHelper::clearDataCache('comment', $comment->cid);

        $comment->delete();

        return response()->json([
            'code' => 0,
            'message' => ConfigUtility::getCodeMessage(0, 'Fresns', $langTag),
            'data' => $detail,
        ]);
    }

    // delete user
    public function deleteUser()
    {
        $langTag = Cookie::get('fresns_plugin_admin_menu_lang_tag');
        $type = Cookie::get('fresns_plugin_admin_menu_type');

        if ($type != 'user') {
            return response()->json([
                'code' => 30002,
                'message' => ConfigUtility::getCodeMessage(30002, 'Fresns', $langTag),
                'data' => [],
            ]);
        }

        $primaryId = Cookie::get('fresns_plugin_admin_menu_primary_id');
        $user = User::where('id', $primaryId)->first();

        if (empty($user)) {
            return response()->json([
                'code' => 31602,
                'message' => ConfigUtility::getCodeMessage(31602, 'Fresns', $langTag),
                'data' => [],
            ]);
        }

        $detail = DetailUtility::userDetail($user, $langTag);

        $account = Account::with(['users'])->where('id', $user->account_id)->first();
        if (count($account?->users) == 1) {
            CacheHelper::forgetFresnsAccount($account->aid);
            $account->delete();
        }

        CacheHelper::forgetFresnsUser($user->uid, $user->id);
        $user->delete();

        return response()->json([
            'code' => 0,
            'message' => ConfigUtility::getCodeMessage(0, 'Fresns', $langTag),
            'data' => $detail,
        ]);
    }

    // edit post
    public function editPost(Request $request)
    {
        $langTag = Cookie::get('fresns_plugin_admin_menu_lang_tag');
        $type = Cookie::get('fresns_plugin_admin_menu_type');

        if ($type != 'post') {
            return response()->json([
                'code' => 30002,
                'message' => ConfigUtility::getCodeMessage(30002, 'Fresns', $langTag),
                'data' => [],
            ]);
        }

        $primaryId = Cookie::get('fresns_plugin_admin_menu_primary_id');
        $post = Post::where('id', $primaryId)->first();

        if (empty($post)) {
            return response()->json([
                'code' => 37400,
                'message' => ConfigUtility::getCodeMessage(37400, 'Fresns', $langTag),
                'data' => [],
            ]);
        }

        $inputName = $request->inputName;
        $newValue = $request->newValue;

        switch ($inputName) {
            case 'group':
                if ($post->group_id) {
                    $oldGroup = Group::whereId($post->group_id)->first();

                    $oldGroup?->decrement('post_count');

                    if ($post->digest_state != Post::DIGEST_NO) {
                        $oldGroup?->decrement('post_digest_count');
                    }

                    if ($post->comment_count && $oldGroup->comment_count > $post->comment_count) {
                        $oldGroup?->decrement('comment_count', $post->comment_count);
                    }

                    if ($post->comment_digest_count) {
                        $oldGroup?->decrement('comment_digest_count', $post->comment_digest_count);
                    }
                }

                if ($newValue) {
                    $newGroup = Group::where('gid', $newValue)->first();

                    $post->update([
                        'group_id' => $newGroup?->id ?? 0,
                    ]);

                    $newGroup?->increment('post_count');

                    if ($post->digest_state != Post::DIGEST_NO) {
                        $newGroup?->increment('post_digest_count');
                    }

                    if ($post->comment_count) {
                        $newGroup?->increment('comment_count', $post->comment_count);
                    }

                    if ($post->comment_digest_count) {
                        $newGroup?->increment('comment_digest_count', $post->comment_digest_count);
                    }
                } else {
                    $post->update([
                        'group_id' => 0,
                    ]);
                }
                break;

            case 'title':
                $post->update([
                    'title' => $newValue,
                ]);
                break;

            case 'digestState':
                InteractionUtility::markContentDigest('post', $post->id, $newValue);
                break;

            case 'stickyState':
                InteractionUtility::markContentSticky('post', $post->id, $newValue);
                break;

            case 'status':
                $currentStatus = $post->is_enabled;

                if ($currentStatus) {
                    $post->update([
                        'is_enabled' => false,
                    ]);
                } else {
                    $post->update([
                        'is_enabled' => true,
                    ]);
                }
                break;
        }

        CacheHelper::clearDataCache('post', $post->pid);

        $authUid = Cookie::get('fresns_plugin_admin_menu_auth_uid');
        $timezone = Cookie::get('fresns_plugin_admin_menu_timezone');
        $viewType = Cookie::get('fresns_plugin_admin_menu_view_type');

        $authUserId = PrimaryHelper::fresnsPrimaryId('user', $authUid);

        $detail = DetailUtility::postDetail($post->pid, $langTag, $timezone, $authUserId, ['viewType' => $viewType]);

        return response()->json([
            'code' => 0,
            'message' => ConfigUtility::getCodeMessage(0, 'Fresns', $langTag),
            'data' => $detail,
        ]);
    }

    // edit comment
    public function editComment(Request $request)
    {
        $langTag = Cookie::get('fresns_plugin_admin_menu_lang_tag');
        $type = Cookie::get('fresns_plugin_admin_menu_type');

        if ($type != 'comment') {
            return response()->json([
                'code' => 30002,
                'message' => ConfigUtility::getCodeMessage(30002, 'Fresns', $langTag),
                'data' => [],
            ]);
        }

        $primaryId = Cookie::get('fresns_plugin_admin_menu_primary_id');
        $comment = Comment::where('id', $primaryId)->first();

        if (empty($comment)) {
            return response()->json([
                'code' => 37500,
                'message' => ConfigUtility::getCodeMessage(37500, 'Fresns', $langTag),
                'data' => [],
            ]);
        }

        $inputName = $request->inputName;
        $newValue = $request->newValue;

        switch ($inputName) {
            case 'digestState':
                InteractionUtility::markContentDigest('comment', $comment->id, $newValue);
                break;

            case 'isSticky':
                $isSticky = ($newValue == 'true') ? 1 : 0;

                InteractionUtility::markContentSticky('comment', $comment->id, $isSticky);
                break;

            case 'status':
                $currentStatus = $comment->is_enabled;

                if ($currentStatus) {
                    $comment->update([
                        'is_enabled' => false,
                    ]);
                } else {
                    $comment->update([
                        'is_enabled' => true,
                    ]);
                }
                break;
        }

        CacheHelper::clearDataCache('comment', $comment->cid);

        $authUid = Cookie::get('fresns_plugin_admin_menu_auth_uid');
        $timezone = Cookie::get('fresns_plugin_admin_menu_timezone');
        $viewType = Cookie::get('fresns_plugin_admin_menu_view_type');

        $authUserId = PrimaryHelper::fresnsPrimaryId('user', $authUid);

        $detail = DetailUtility::commentDetail($comment->pid, $langTag, $timezone, $authUserId, ['viewType' => $viewType]);

        return response()->json([
            'code' => 0,
            'message' => ConfigUtility::getCodeMessage(0, 'Fresns', $langTag),
            'data' => $detail,
        ]);
    }

    // edit user
    public function editUser(Request $request)
    {
        $langTag = Cookie::get('fresns_plugin_admin_menu_lang_tag');
        $type = Cookie::get('fresns_plugin_admin_menu_type');

        if ($type != 'user') {
            return response()->json([
                'code' => 30002,
                'message' => ConfigUtility::getCodeMessage(30002, 'Fresns', $langTag),
                'data' => [],
            ]);
        }

        $primaryId = Cookie::get('fresns_plugin_admin_menu_primary_id');
        $user = User::where('id', $primaryId)->first();

        if (empty($user)) {
            return response()->json([
                'code' => 31602,
                'message' => ConfigUtility::getCodeMessage(31602, 'Fresns', $langTag),
                'data' => [],
            ]);
        }

        $requestType = $request->type;

        switch ($requestType) {
            case 'avatar':
                $user->update([
                    'avatar_file_id' => null,
                    'avatar_file_url' => null,
                ]);
                break;

            case 'nickname':
                if (empty($request->nickname)) {
                    return response()->json([
                        'code' => 30001,
                        'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
                        'data' => [],
                    ]);
                }

                if ($user->nickname == $request->nickname) {
                    return response()->json([
                        'code' => 30006,
                        'message' => ConfigUtility::getCodeMessage(30006, 'Fresns', $langTag),
                        'data' => [],
                    ]);
                }

                $user->update([
                    'nickname' => $request->nickname,
                ]);
                break;

            case 'username':
                if (empty($request->username)) {
                    return response()->json([
                        'code' => 30001,
                        'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
                        'data' => [],
                    ]);
                }

                if ($user->username == $request->username) {
                    return response()->json([
                        'code' => 30006,
                        'message' => ConfigUtility::getCodeMessage(30006, 'Fresns', $langTag),
                        'data' => [],
                    ]);
                }

                $user->update([
                    'username' => $request->username,
                ]);
                break;

            case 'role':
                if (empty($request->rid)) {
                    return response()->json([
                        'code' => 30001,
                        'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
                        'data' => [],
                    ]);
                }

                $userRole = UserRole::where('user_id', $user->id)->where('is_main', 1)->first();

                $role = Role::where('rid', $request->rid)->first();

                if (empty($role)) {
                    return response()->json([
                        'code' => 36100,
                        'message' => ConfigUtility::getCodeMessage(36100, 'Fresns', $langTag),
                        'data' => [],
                    ]);
                }

                if ($userRole->role_id == $role->id) {
                    return response()->json([
                        'code' => 30006,
                        'message' => ConfigUtility::getCodeMessage(30006, 'Fresns', $langTag),
                        'data' => [],
                    ]);
                }

                $userRole->delete();

                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                    'is_main' => 1,
                ]);
                break;

            case 'status':
                $currentStatus = $user->is_enabled;

                if ($currentStatus) {
                    $user->update([
                        'is_enabled' => false,
                    ]);
                } else {
                    $user->update([
                        'is_enabled' => true,
                    ]);
                }
                break;
        }

        CacheHelper::forgetFresnsUser($user->id, $user->uid);

        $authUid = Cookie::get('fresns_plugin_admin_menu_auth_uid');
        $timezone = Cookie::get('fresns_plugin_admin_menu_timezone');
        $viewType = Cookie::get('fresns_plugin_admin_menu_view_type');

        $authUserId = PrimaryHelper::fresnsPrimaryId('user', $authUid);

        $detail = DetailUtility::userDetail($user->uid, $langTag, $timezone, $authUserId, ['viewType' => $viewType]);

        return response()->json([
            'code' => 0,
            'message' => ConfigUtility::getCodeMessage(0, 'Fresns', $langTag),
            'data' => $detail,
        ]);
    }
}
