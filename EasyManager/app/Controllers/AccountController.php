<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasyManager\Controllers;

use App\Helpers\CacheHelper;
use App\Models\Account;
use App\Models\AccountConnect;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $accountQuery = Account::with(['wallet', 'users']);

        $accountQuery->when($request->type, function ($query, $value) {
            $query->where('type', $value);
        });

        $accountQuery->when($request->id, function ($query, $value) {
            $query->where('id', $value);
        });

        $accountQuery->when($request->aid, function ($query, $value) {
            $query->where('aid', $value);
        });

        $accountQuery->when($request->email, function ($query, $value) {
            $query->where('email', $value);
        });

        $accountQuery->when($request->phone, function ($query, $value) {
            $query->where('pure_phone', $value);
        });

        $accountQuery->when($request->wait_delete, function ($query, $value) {
            $query->where('wait_delete', $value);
        });

        $accountQuery->when($request->orderBy, function ($query, $value) {
            $query->$value();
        });

        if (empty($request->orderBy)) {
            $accountQuery->latest();
        }

        $accounts = $accountQuery->paginate($request->get('pageSize', 15));

        // search config
        $search = [
            'status' => true,
            'action' => route('easy-manager.account.index'),
            'selects' => [
                [
                    'name' => 'AID',
                    'value' => 'aid',
                ],
                [
                    'name' => __('EasyManager::fresns.table_phone'),
                    'value' => 'email',
                ],
                [
                    'name' => __('EasyManager::fresns.table_email'),
                    'value' => 'phone',
                ],
            ],
            'defaultSelect' => [
                'name' => 'AID',
                'value' => 'aid',
            ],
        ];

        return view('EasyManager::account', compact('accounts', 'search'));
    }

    public function update(Account $account, Request $request)
    {
        $account->is_enable = $request->is_enable;
        $account->save();

        CacheHelper::forgetFresnsAccount($account->aid);

        return $this->updateSuccess();
    }

    public function destroy(Account $account, Request $request)
    {
        foreach ($account?->users as $user) {
            $user->delete();
        }

        AccountConnect::where('account_id', $account->id)->forceDelete();

        $account->delete();

        return $this->deleteSuccess();
    }
}
