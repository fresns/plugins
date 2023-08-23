<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EditorWorkspace\Controllers;

use App\Helpers\ConfigHelper;
use App\Helpers\PrimaryHelper;
use App\Models\Account;
use App\Models\Config;
use App\Models\File;
use App\Models\FileUsage;
use App\Models\User;
use App\Utilities\ConfigUtility;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $accountConfig = Config::where('item_key', 'editor_workspace_accounts')->first();

        $accountIds = $accountConfig?->item_value ?? [];

        $accounts = [];
        if ($accountIds) {
            $accounts = Account::with(['users'])->whereIn('id', $accountIds)->get();
        }

        $countryCode = ConfigHelper::fresnsConfigByItemKeys([
            'send_sms_default_code',
            'send_sms_supported_codes',
        ]);

        return view('EditorWorkspace::admin.index', compact('accounts', 'countryCode'));
    }

    public function users(Request $request)
    {
        $accountId = $request->accountId;
        if (! $accountId) {
            return back()->with('failure', __('FsLang::tips.account_not_found'));
        }

        $users = User::with(['stat'])->where('account_id', $accountId)->get();

        // site config
        $configKeys = ConfigHelper::fresnsConfigByItemKeys([
            'user_identifier',
            'website_user_detail_path',
            'site_url',
        ]);

        $url = $configKeys['site_url'].'/'.$configKeys['website_user_detail_path'].'/';
        $identifier = $configKeys['user_identifier'];

        return view('EditorWorkspace::admin.users', compact('users', 'url', 'identifier'));
    }

    public function accountAdd(Request $request)
    {
        $accountName = $request->accountName;

        filter_var($accountName, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $accountName :
            $credentials['phone'] = $accountName;

        $account = Account::where($credentials)->first();

        if (! $account) {
            return back()->with('failure', __('FsLang::tips.account_not_found'));
        }

        $accountConfig = Config::where('item_key', 'editor_workspace_accounts')->first();
        $accountIds = $accountConfig?->item_value ?? [];

        if (! in_array($account->id, $accountIds)) {
            $accountIds[] = $account->id;
        }

        $fresnsConfigItems = [
            [
                'item_key' => 'editor_workspace_accounts',
                'item_value' => $accountIds,
                'item_type' => 'array',
                'item_tag' => 'EditorWorkspace',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
        ];

        ConfigUtility::changeFresnsConfigItems($fresnsConfigItems);

        return $this->updateSuccess();
    }

    public function accountRemove(Request $request)
    {
        $accountId = $request->accountId;

        $accountConfig = Config::where('item_key', 'editor_workspace_accounts')->first();
        $accountIds = $accountConfig?->item_value ?? [];

        if (($key = array_search($accountId, $accountIds)) !== false) {
            unset($accountIds[$key]);
        }

        $fresnsConfigItems = [
            [
                'item_key' => 'editor_workspace_accounts',
                'item_value' => $accountIds,
                'item_type' => 'array',
                'item_tag' => 'EditorWorkspace',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
        ];

        ConfigUtility::changeFresnsConfigItems($fresnsConfigItems);

        return $this->deleteSuccess();
    }

    public function accountGenerate(Request $request)
    {
        $type = $request->type;

        if (empty($type)) {
            return back()->with('failure', 'Missing account type');
        }

        $account = match ($type) {
            'email' => $request->email,
            'phone' => $request->phone,
            default => null,
        };

        if (empty($account)) {
            return back()->with('failure', 'Missing account email or phone');
        }

        $country_code = $request->country_code;

        if ($type == 'phone' && empty($country_code)) {
            return back()->with('failure', 'Missing country code');
        }

        $nickname = $request->nickname;

        if (empty($nickname)) {
            return back()->with('failure', 'Missing nickname');
        }

        $typeInt = match ($type) {
            'email' => 1,
            'phone' => 2,
        };

        $file = $request->file('avatar_file');

        $wordBody = [
            'type' => $typeInt,
            'account' => $account,
            'countryCode' => $country_code,
            'password' => $request->password,
            'createUser' => true,
            'userInfo' => [
                'username' => $request->username,
                'nickname' => $nickname,
                'avatarUrl' => $file ? null : $request->avatar_file_url,
                'gender' => $request->gender,
            ],
        ];

        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->createAccount($wordBody);

        if ($fresnsResp->isErrorResponse()) {
            return back()->with('failure', $fresnsResp->getMessage());
        }

        // add config
        $accountModel = Account::where('aid', $fresnsResp->getData('aid'))->first();

        $accountConfig = Config::where('item_key', 'editor_workspace_accounts')->first();

        $accountIds = $accountConfig?->item_value ?? [];

        if (! in_array($accountModel->id, $accountIds)) {
            $accountIds[] = $accountModel->id;
        }

        $fresnsConfigItems = [
            [
                'item_key' => 'editor_workspace_accounts',
                'item_value' => $accountIds,
                'item_type' => 'array',
                'item_tag' => 'EditorWorkspace',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
        ];

        ConfigUtility::changeFresnsConfigItems($fresnsConfigItems);

        $fileId = null;
        if ($file) {
            $fileWordBody = [
                'usageType' => FileUsage::TYPE_USER,
                'platformId' => 4,
                'tableName' => 'users',
                'tableColumn' => 'avatar_file_id',
                'tableKey' => (string) $fresnsResp->getData('uid'),
                'type' => File::TYPE_IMAGE,
                'file' => $file,
            ];
            $uploadResp = \FresnsCmdWord::plugin('Fresns')->uploadFile($fileWordBody);

            if ($uploadResp->isErrorResponse()) {
                return back()->with('failure', $uploadResp->getMessage());
            }

            $fileId = PrimaryHelper::fresnsFileIdByFid($uploadResp->getData('fid'));
        }

        if ($request->bio || $fileId) {
            $user = User::where('uid', $fresnsResp->getData('uid'))->first();

            $user?->update([
                'avatar_file_id' => $fileId,
                'bio' => $request->bio,
            ]);
        }

        return $this->createSuccess();
    }

    public function userGenerate(Request $request)
    {
        $aid = $request->aid;

        if (empty($aid)) {
            return back()->with('failure', 'Missing account aid');
        }

        $nickname = $request->nickname;

        if (empty($nickname)) {
            return back()->with('failure', 'Missing nickname');
        }

        $file = $request->file('avatar_file');

        $wordBody = [
            'aid' => $aid,
            'username' => $request->username,
            'nickname' => $nickname,
            'avatarUrl' => $file ? null : $request->avatar_file_url,
            'gender' => $request->gender,
        ];

        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->createUser($wordBody);

        if ($fresnsResp->isErrorResponse()) {
            return back()->with('failure', $fresnsResp->getMessage());
        }

        $fileId = null;
        if ($file) {
            $fileWordBody = [
                'usageType' => FileUsage::TYPE_USER,
                'platformId' => 4,
                'tableName' => 'users',
                'tableColumn' => 'avatar_file_id',
                'tableKey' => (string) $fresnsResp->getData('uid'),
                'type' => File::TYPE_IMAGE,
                'file' => $file,
            ];
            $uploadResp = \FresnsCmdWord::plugin('Fresns')->uploadFile($fileWordBody);

            if ($uploadResp->isErrorResponse()) {
                return back()->with('failure', $uploadResp->getMessage());
            }

            $fileId = PrimaryHelper::fresnsFileIdByFid($uploadResp->getData('fid'));
        }

        if ($request->bio || $fileId) {
            $user = User::where('uid', $fresnsResp->getData('uid'))->first();

            $user?->update([
                'avatar_file_id' => $fileId,
                'bio' => $request->bio,
            ]);
        }

        return $this->createSuccess();
    }
}
