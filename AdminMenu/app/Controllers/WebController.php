<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\AdminMenu\Controllers;

use App\Fresns\Api\Services\CommentService;
use App\Fresns\Api\Services\PostService;
use App\Fresns\Api\Services\UserService;
use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\FileHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\PrimaryHelper;
use App\Models\Comment;
use App\Models\CommentLog;
use App\Models\Group;
use App\Models\Post;
use App\Models\PostLog;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Utilities\ConfigUtility;
use App\Utilities\InteractionUtility;
use App\Utilities\PermissionUtility;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class WebController extends Controller
{
    public function index(Request $request)
    {
        // Verify URL Authorization
        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->verifyUrlAuthorization([
            'urlAuthorization' => $request->authorization,
        ]);

        $langTag = $fresnsResp->getData('langTag');
        View::share('langTag', $langTag);

        if ($fresnsResp->isErrorResponse()) {
            return view('AdminMenu::error', [
                'code' => $fresnsResp->getCode(),
                'message' => $fresnsResp->getMessage(),
            ]);
        }

        // Check User Login
        if (! $fresnsResp->getData('uid')) {
            return view('AdminMenu::error', [
                'code' => 31601,
                'message' => ConfigUtility::getCodeMessage(31601, 'Fresns', $langTag),
            ]);
        }

        // Judgment Source
        $type = 'user';
        $groupId = null;
        if ($request->cid) {
            $type = 'comment';
            $model = PrimaryHelper::fresnsModelByFsid('comment', $request->cid);

            $groupId = $model->post->group_id;
        } elseif ($request->pid) {
            $type = 'post';
            $model = PrimaryHelper::fresnsModelByFsid('post', $request->pid);

            $groupId = $model->group_id;
        } else {
            $model = PrimaryHelper::fresnsModelByFsid('user', $request->uid);
        }

        // Verify the right to use
        $userId = PrimaryHelper::fresnsUserIdByUidOrUsername($fresnsResp->getData('uid'));
        $checkPerm = PermissionUtility::checkExtendPerm('AdminMenu', 'manage', $groupId, $userId);
        if (! $checkPerm) {
            return view('AdminMenu::error', [
                'code' => 35301,
                'message' => ConfigUtility::getCodeMessage(35301, 'Fresns', $langTag),
            ]);
        }

        $timezone = ConfigHelper::fresnsConfigDefaultTimezone();

        $groupCategories = [];
        $roles = [];

        switch ($type) {
            // post
            case 'post':
                $service = new PostService();
                $data = $service->postData($model, 'list', $langTag, $timezone, false);

                // group categories
                $groupQuery = Group::where('type', Group::TYPE_CATEGORY)->orderBy('rating')->isEnable()->get();

                $catList = [];
                foreach ($groupQuery as $category) {
                    $item = $category->getCategoryInfo($langTag);
                    $catList[] = $item;
                }

                $groupCategories = $catList;
            break;

            // comment
            case 'comment':
                $service = new CommentService();
                $data = $service->commentData($model, 'list', $langTag, $timezone, false);
            break;

            // user
            case 'user':
                $service = new UserService();
                $data = $service->userData($model, $langTag, $timezone);

                // get roles
                $roleQuery = Role::orderBy('rating')->get();

                $roleList = [];
                foreach ($roleQuery as $role) {
                    $item['rid'] = $role->id;
                    $item['nicknameColor'] = $role->nickname_color;
                    $item['name'] = LanguageHelper::fresnsLanguageByTableId('roles', 'name', $role->id, $langTag);
                    $item['nameDisplay'] = (bool) $role->is_display_name;
                    $item['icon'] = FileHelper::fresnsFileUrlByTableColumn($role->icon_file_id, $role->icon_file_url);
                    $item['iconDisplay'] = (bool) $role->is_display_icon;
                    $item['status'] = (bool) $role->is_enable;
                    $roleList[] = $item;
                }
                $roles = $roleList;
            break;
        }

        $fsLang = ConfigHelper::fresnsConfigByItemKey('language_pack_contents', $langTag);
        $fsName = ConfigHelper::fresnsConfigByItemKeys([
            'user_name',
            'user_uid_name',
            'user_username_name',
            'user_nickname_name',
            'user_role_name',
            'group_name',
        ], $langTag);

        $authUlid = (string) Str::ulid();
        $cacheTags = ['fresnsPlugins', 'pluginAdminMenu', 'fresnsPluginAuth'];

        CacheHelper::put($authUlid, $authUlid, $cacheTags, null, now()->addMinutes(10));

        return view('AdminMenu::index', compact('type', 'data', 'roles', 'groupCategories', 'authUlid', 'langTag', 'fsLang', 'fsName'));
    }

    // delete post
    public function deletePost(Request $request)
    {
        $langTag = $request->langTag ?? ConfigHelper::fresnsConfigDefaultLangTag();
        View::share('langTag', $langTag);

        if (! $request->authUlid || ! $request->pid) {
            return view('AdminMenu::error', [
                'code' => 30001,
                'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        $cacheTags = ['fresnsPlugins', 'pluginAdminMenu', 'fresnsPluginAuth'];
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

        InteractionUtility::publishStats('post', $post->id, 'decrement');

        PostLog::where('post_id', $post->id)->delete();

        CacheHelper::clearDataCache('post', $request->pid, 'fresnsApiData');
        CacheHelper::clearDataCache('post', $request->pid, 'fresnsSeo');
        CacheHelper::clearDataCache('post', $request->pid, 'fresnsModel');

        $post->delete();

        return view('AdminMenu::error', [
            'code' => 0,
            'message' => ConfigUtility::getCodeMessage(0, 'Fresns', $langTag),
        ]);
    }

    // edit post group
    public function editPostGroup(Request $request)
    {
        $langTag = $request->langTag ?? ConfigHelper::fresnsConfigDefaultLangTag();
        View::share('langTag', $langTag);

        if (! $request->authUlid || ! $request->pid) {
            return view('AdminMenu::error', [
                'code' => 30001,
                'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        $cacheTags = ['fresnsPlugins', 'pluginAdminMenu', 'fresnsPluginAuth'];
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

        if ($post->group_id) {
            $oldGroup = Group::whereId($post->group_id)->first();

            $oldGroup?->decrement('post_count');

            if ($post->comment_count) {
                $oldGroup?->decrement('comment_count', $post->comment_count);
            }
        }

        if ($request->gid) {
            $newGroup = Group::where('gid', $request->gid)->first();

            $post->update([
                'group_id' => $newGroup?->id ?? 0,
            ]);

            $newGroup?->increment('post_count');

            if ($post->comment_count) {
                $newGroup?->increment('comment_count', $post->comment_count);
            }
        } else {
            $post->update([
                'group_id' => 0,
            ]);
        }

        CacheHelper::clearDataCache('post', $request->pid, 'fresnsApiData');
        CacheHelper::clearDataCache('post', $request->pid, 'fresnsSeo');
        CacheHelper::clearDataCache('post', $request->pid, 'fresnsModel');

        return view('AdminMenu::error', [
            'code' => 0,
            'message' => ConfigUtility::getCodeMessage(0, 'Fresns', $langTag),
        ]);
    }

    // edit post
    public function editPost(Request $request)
    {
        $langTag = $request->langTag ?? ConfigHelper::fresnsConfigDefaultLangTag();
        View::share('langTag', $langTag);

        if (! $request->authUlid || ! $request->pid) {
            return view('AdminMenu::error', [
                'code' => 30001,
                'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        $cacheTags = ['fresnsPlugins', 'pluginAdminMenu', 'fresnsPluginAuth'];
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

        if ($request->title) {
            $post->update([
                'title' => $request->title,
            ]);
        }

        if ($request->digestState) {
            $post->update([
                'digest_state' => $request->digestState,
            ]);
        }

        if ($request->stickyState) {
            $post->update([
                'sticky_state' => $request->stickyState,
            ]);
        }

        if ($request->status == 'true') {
            $post->update([
                'is_enable' => true,
            ]);
        }

        if ($request->status == 'false') {
            $post->update([
                'is_enable' => false,
            ]);
        }

        CacheHelper::clearDataCache('post', $request->pid, 'fresnsApiData');
        CacheHelper::clearDataCache('post', $request->pid, 'fresnsSeo');
        CacheHelper::clearDataCache('post', $request->pid, 'fresnsModel');

        return view('AdminMenu::error', [
            'code' => 0,
            'message' => ConfigUtility::getCodeMessage(0, 'Fresns', $langTag),
        ]);
    }

    // delete comment
    public function deleteComment(Request $request)
    {
        $langTag = $request->langTag ?? ConfigHelper::fresnsConfigDefaultLangTag();
        View::share('langTag', $langTag);

        if (! $request->authUlid || ! $request->cid) {
            return view('AdminMenu::error', [
                'code' => 30001,
                'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        $cacheTags = ['fresnsPlugins', 'pluginAdminMenu', 'fresnsPluginAuth'];
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
                'code' => 37400,
                'message' => ConfigUtility::getCodeMessage(37400, 'Fresns', $langTag),
            ]);
        }

        InteractionUtility::publishStats('comment', $comment->id, 'decrement');

        CommentLog::where('comment_id', $comment->id)->delete();

        CacheHelper::clearDataCache('comment', $request->cid, 'fresnsApiData');
        CacheHelper::clearDataCache('comment', $request->cid, 'fresnsSeo');
        CacheHelper::clearDataCache('comment', $request->cid, 'fresnsModel');

        $comment->delete();

        return view('AdminMenu::error', [
            'code' => 0,
            'message' => ConfigUtility::getCodeMessage(0, 'Fresns', $langTag),
        ]);
    }

    // edit comment
    public function editComment(Request $request)
    {
        $langTag = $request->langTag ?? ConfigHelper::fresnsConfigDefaultLangTag();
        View::share('langTag', $langTag);

        if (! $request->authUlid || ! $request->cid) {
            return view('AdminMenu::error', [
                'code' => 30001,
                'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        $cacheTags = ['fresnsPlugins', 'pluginAdminMenu', 'fresnsPluginAuth'];
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

        if ($request->digestState) {
            $comment->update([
                'digest_state' => $request->digestState,
            ]);
        }

        if ($request->isSticky == 'true') {
            $comment->update([
                'is_sticky' => true,
            ]);
        }

        if ($request->isSticky == 'false') {
            $comment->update([
                'is_sticky' => false,
            ]);
        }

        if ($request->status == 'true') {
            $comment->update([
                'is_enable' => true,
            ]);
        }

        if ($request->status == 'false') {
            $comment->update([
                'is_enable' => false,
            ]);
        }

        CacheHelper::clearDataCache('comment', $request->cid, 'fresnsApiData');
        CacheHelper::clearDataCache('comment', $request->cid, 'fresnsSeo');
        CacheHelper::clearDataCache('comment', $request->cid, 'fresnsModel');

        return view('AdminMenu::error', [
            'code' => 0,
            'message' => ConfigUtility::getCodeMessage(0, 'Fresns', $langTag),
        ]);
    }

    // edit user
    public function editUser(Request $request)
    {
        $langTag = $request->langTag ?? ConfigHelper::fresnsConfigDefaultLangTag();
        View::share('langTag', $langTag);

        if (! $request->authUlid || ! $request->uid) {
            return view('AdminMenu::error', [
                'code' => 30001,
                'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        $cacheTags = ['fresnsPlugins', 'pluginAdminMenu', 'fresnsPluginAuth'];
        $authUlid = CacheHelper::get($request->authUlid, $cacheTags);

        if (empty($authUlid)) {
            return view('AdminMenu::error', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
            ]);
        }

        $user = User::where('uid', $request->uid)->first();

        if (empty($user)) {
            return view('AdminMenu::error', [
                'code' => 37400,
                'message' => ConfigUtility::getCodeMessage(37400, 'Fresns', $langTag),
            ]);
        }

        if ($request->avatar) {
            $user->update([
                'avatar_file_id' => null,
                'avatar_file_url' => null,
            ]);
        }

        if ($request->nickname) {
            $user->update([
                'nickname' => $request->nickname,
            ]);
        }

        if ($request->username) {
            $user->update([
                'username' => $request->username,
            ]);
        }

        if ($request->roleId) {
            UserRole::where('user_id', $user->id)->where('is_main', 1)->delete();

            $role = Role::where('id', $request->roleId)->first();

            if (empty($role)) {
                return view('AdminMenu::error', [
                    'code' => 36100,
                    'message' => ConfigUtility::getCodeMessage(36100, 'Fresns', $langTag),
                ]);
            }

            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'is_main' => 1,
            ]);
        }

        if ($request->status == 'true') {
            $user->update([
                'is_enable' => true,
            ]);
        }

        if ($request->status == 'false') {
            $user->update([
                'is_enable' => false,
            ]);
        }

        CacheHelper::forgetFresnsUser($user->id, $user->uid);

        return view('AdminMenu::error', [
            'code' => 0,
            'message' => ConfigUtility::getCodeMessage(0, 'Fresns', $langTag),
        ]);
    }
}
