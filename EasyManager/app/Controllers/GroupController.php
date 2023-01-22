<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasyManager\Controllers;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Models\Group;
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

        CacheHelper::forgetFresnsKey('fresns_guest_all_groups');
        CacheHelper::forgetFresnsTag('fresnsGroupData');

        return $this->updateSuccess();
    }
}
