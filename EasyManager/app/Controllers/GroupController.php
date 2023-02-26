<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasyManager\Controllers;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Post;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $groupQuery = Group::query();

        $groupQuery->when($request->id, function ($query, $value) {
            $query->where('id', $value);
        });

        $groupQuery->when($request->gid, function ($query, $value) {
            $query->where('gid', $value);
        });

        $groupQuery->when($request->name, function ($query, $value) {
            $query->where('name', 'like', '%'.$value.'%');
        });

        $orderBy = $request->orderBy ?: 'created_at';
        $orderDirection = $request->orderDirection ?: 'asc';
        $groupQuery->orderBy($orderBy, $orderDirection);

        $groups = $groupQuery->paginate($request->get('pageSize', 15));

        // search config
        $search = [
            'status' => true,
            'action' => route('easy-manager.group.index'),
            'selects' => [
                [
                    'name' => 'GID',
                    'value' => 'gid',
                ],
                [
                    'name' => __('EasyManager::fresns.table_name'),
                    'value' => 'name',
                ],
            ],
            'defaultSelect' => [
                'name' => 'GID',
                'value' => 'gid',
            ],
        ];

        // site config
        $configKeys = ConfigHelper::fresnsConfigByItemKeys([
            'website_group_detail_path',
            'site_url',
            'group_liker_count',
            'group_disliker_count',
            'group_follower_count',
            'group_blocker_count',
        ]);
        $url = $configKeys['site_url'].'/'.$configKeys['website_group_detail_path'].'/';

        return view('EasyManager::group', compact('groups', 'search', 'url'));
    }

    public function update(Group $group, Request $request)
    {
        if ($group->gid == $request->gid) {
            return back();
        }

        $groupModel = Group::where('gid', $request->gid)->first();
        if ($groupModel) {
            return back()->with('failure', __('EasyManager::fresns.gid_in_use'));
        }

        $group->gid = $request->gid;
        $group->save();

        CacheHelper::forgetFresnsKey('fresns_guest_all_groups', 'fresnsGroupData');
        CacheHelper::forgetFresnsTag('fresnsGroupData');

        return $this->updateSuccess();
    }

    public function groupEditPermissions(int $groupId)
    {
        $group = Group::where('id', $groupId)->first();

        $permissions = [];
        foreach ($group->permissions as $permKey => $permValue) {
            $fresnsKeys = [
                'publish_post',
                'publish_post_roles',
                'publish_post_review',
                'publish_comment',
                'publish_comment_roles',
                'publish_comment_review',
            ];

            $isCustom = true;
            if (in_array($permKey, $fresnsKeys)) {
                $isCustom = false;
            }

            if ($permKey == 'publish_post_roles' || $permKey == 'publish_comment_roles') {
                $permValue = json_encode($permValue);
            }

            if ($permKey == 'publish_post_review' || $permKey == 'publish_comment_review') {
                $permValue = $permValue ? 'true' : 'false';
            }

            $item['permKey'] = $permKey;
            $item['permValue'] = $permValue;
            $item['isCustom'] = $isCustom;

            $permissions[] = $item;
        }

        // search config
        $search = [
            'status' => false,
            'action' => null,
            'selects' => [],
            'defaultSelect' => [],
        ];

        return view('EasyManager::group-edit', compact('group', 'permissions', 'search'));
    }

    public function groupUpdatePermissions(int $groupId, Request $request)
    {
        $group = Group::where('id', $groupId)->first();

        $requestPerms = collect($request->editPermissions['permKey'] ?? [])->filter()->map(function ($value, $key) use ($request) {
            return [
                'permKey' => $value,
                'permValue' => $request->editPermissions['permValue'][$key] ?? '',
            ];
        });

        $permissions = $group->permissions;

        $newPermissions = [];
        foreach ($requestPerms as $newPerm) {
            $permKey = $newPerm['permKey'];
            $permValue = $newPerm['permValue'];

            $newPermissions[$permKey] = $permValue;
        }

        $newPermissions['publish_post'] = $permissions['publish_post'];
        $newPermissions['publish_post_roles'] = $permissions['publish_post_roles'] ?? [];
        $newPermissions['publish_post_review'] = (bool) ($permissions['publish_post_review'] ?? 0);
        $newPermissions['publish_comment'] = $permissions['publish_comment'];
        $newPermissions['publish_comment_roles'] = $permissions['publish_comment_roles'] ?? [];
        $newPermissions['publish_comment_review'] = (bool) ($permissions['publish_comment_review'] ?? 0);

        $group->permissions = $newPermissions;
        $group->save();

        CacheHelper::forgetFresnsMultilingual("fresns_api_group_{$group->gid}", 'fresnsGroupData');
        CacheHelper::forgetFresnsModel('group', $group->gid);

        return $this->updateSuccess();
    }

    public function updateCount()
    {
        $groups = Group::get();

        foreach ($groups as $group) {
            $id = $group->id;

            $postCount = Post::where('group_id', $id)->count();

            $postDigestCount = Post::where('group_id', $id)->where('digest_state', "!=", '1')->count();

            $commentCount = Comment::with(['post'])->whereHas('post', function ($query) use ($id) {
                $query->where('group_id', $id);
            })->count();

            $commentDigestCount = Comment::with(['post'])->where('digest_state', "!=", '1')->whereHas('post', function ($query) use ($id) {
                $query->where('group_id', $id);
            })->count();

            $group->update([
                'post_count' => $postCount,
                'post_digest_count' => $postDigestCount,
                'comment_count' => $commentCount,
                'comment_digest_count' => $commentDigestCount,
            ]);
        }

        return $this->updateSuccess();
    }
}
