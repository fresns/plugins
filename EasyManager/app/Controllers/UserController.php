<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasyManager\Controllers;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserStat;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $userQuery = UserStat::with('profile');

        $userQuery->when($request->accountId, function ($query, $value) {
            $query->whereRelation('profile', 'account_id', $value);
        });

        $userQuery->when($request->id, function ($query, $value) {
            $query->whereRelation('profile', 'id', $value);
        });

        $userQuery->when($request->uid, function ($query, $value) {
            $query->whereRelation('profile', 'uid', $value);
        });

        $userQuery->when($request->username, function ($query, $value) {
            $query->whereRelation('profile', 'username', $value);
        });

        $userQuery->when($request->username, function ($query, $value) {
            $query->whereRelation('profile', 'nickname', 'like', '%'.$value.'%');
        });

        $userQuery->when($request->waitDelete, function ($query, $value) {
            $query->whereRelation('profile', 'wait_delete', $value);
        });

        $orderBy = $request->orderBy ?: 'created_at';
        $orderDirection = $request->orderDirection ?: 'desc';
        $userQuery->orderBy($orderBy, $orderDirection);

        $users = $userQuery->paginate($request->get('pageSize', 15));

        // search config
        $search = [
            'status' => true,
            'action' => route('easy-manager.user.index'),
            'selects' => [
                [
                    'name' => 'UID',
                    'value' => 'uid',
                ],
                [
                    'name' => 'AID',
                    'value' => 'aid',
                ],
                [
                    'name' => __('EasyManager::fresns.table_username'),
                    'value' => 'username',
                ],
                [
                    'name' => __('EasyManager::fresns.table_nickname'),
                    'value' => 'nickname',
                ],
            ],
            'defaultSelect' => [
                'name' => 'UID',
                'value' => 'uid',
            ],
        ];

        // site config
        $configKeys = ConfigHelper::fresnsConfigByItemKeys([
            'user_identifier',
            'website_user_detail_path',
            'site_url',
            'site_mode',
        ]);

        $url = $configKeys['site_url'].'/'.$configKeys['website_user_detail_path'].'/';
        $identifier = $configKeys['user_identifier'];

        $roles = Role::get();

        return view('EasyManager::user', compact('users', 'search', 'url', 'identifier', 'roles'));
    }

    public function update(User $user, Request $request)
    {
        $user->is_enable = $request->is_enable;
        $user->save();

        CacheHelper::forgetFresnsUser($user->id, $user->uid);

        return $this->updateSuccess();
    }

    public function destroy(User $user, Request $request)
    {
        $user->delete();

        return $this->deleteSuccess();
    }

    public function storeRole(Request $request, int $uid)
    {
        $user = User::where('uid', $uid)->first();

        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $request->roleId,
            'is_main' => $request->is_main,
            'expired_at' => $request->expired_at,
            'restore_role_id' => $request->restore_role_id ? $request->restore_role_id : null,
        ]);

        CacheHelper::forgetFresnsUser($user->id, $user->uid);

        return $this->createSuccess();
    }

    public function deleteRole(int $id)
    {
        $userRole = UserRole::where('id', $id)->first();

        $user = User::where('id', $userRole->user_id)->first();

        CacheHelper::forgetFresnsUser($user->id, $user->uid);

        $userRole->delete();

        return $this->deleteSuccess();
    }
}
