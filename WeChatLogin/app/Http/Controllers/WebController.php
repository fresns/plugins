<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\WeChatLogin\Http\Controllers;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper as FsConfigHelper;
use App\Helpers\PrimaryHelper;
use App\Models\AccountConnect;
use App\Models\PluginCallback;
use App\Utilities\ConfigUtility;
use EasyWeChat\OfficialAccount\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Plugins\WeChatLogin\Helpers\ConfigHelper;
use Plugins\WeChatLogin\Helpers\LoginHelper;

class WebController extends Controller
{
    // 插件入口，判断权限和跳转页面
    public function index(Request $request)
    {
        $connectPlatformId = $request->connectPlatformId;
        if (empty($connectPlatformId)) {
            return view('WeChatLogin::error', [
                'code' => 30001,
                'message' => '[Connect ID] '.ConfigUtility::getCodeMessage(30001, 'Fresns'),
            ]);
        }

        // 验证路径凭证
        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->verifyUrlAuthorization([
            'urlAuthorization' => $request->authorization,
        ]);

        if ($fresnsResp->isErrorResponse()) {
            return view('WeChatLogin::error', [
                'code' => $fresnsResp->getCode(),
                'message' => $fresnsResp->getMessage(),
            ]);
        }

        // auth
        $authUlid = (string) Str::ulid();
        $langTag = $fresnsResp->getData('langTag');

        // account id
        $accountId = PrimaryHelper::fresnsAccountIdByAid($fresnsResp->getData('aid'));

        $cacheData = [
            'ulid' => $authUlid,
            'aid' => $fresnsResp->getData('aid'),
            'accountId' => $accountId,
            'connectPlatformId' => $connectPlatformId,
            'postMessageKey' => $request->postMessageKey,
        ];

        CacheHelper::put($cacheData, $authUlid, ConfigHelper::getAuthCacheTags(), null, now()->addMinutes(10));

        // 跳转
        if ($request->scene == 'connect') {
            // 验证是否登录状态
            if (empty($accountId)) {
                return view('WeChatLogin::error', [
                    'code' => 31501,
                    'message' => ConfigUtility::getCodeMessage(31501, 'Fresns', $langTag),
                ]);
            }

            $connectInfo = AccountConnect::where('account_id', $accountId)->where('connect_platform_id', $connectPlatformId)->first();

            // 已有关联，操作解绑
            if ($connectInfo) {
                return redirect()->to(route('wechat-login.connect.disconnect', [
                    'authUlid' => $authUlid,
                    'langTag' => $langTag,
                ]));
            }

            // 无关联，操作绑定
            return redirect()->to(route('wechat-login.connect.add', [
                'authUlid' => $authUlid,
                'langTag' => $langTag,
            ]));
        }

        // 未登录，授权登录
        return redirect()->to(route('wechat-login.sign.in', [
            'authUlid' => $authUlid,
            'langTag' => $langTag,
        ]));
    }

    // 登录入口，微信中直接弹出授权，微信外显示二维码
    public function signIn(Request $request)
    {
        $authUlid = $request->authUlid;
        $langTag = $request->langTag;

        // 验证使用权限
        $cacheData = CacheHelper::get($authUlid, ConfigHelper::getAuthCacheTags());

        $postMessageKey = $cacheData['postMessageKey'] ?? '';

        if (empty($cacheData)) {
            return view('WeChatLogin::error', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
            ]);
        }

        $connectPlatformId = $cacheData['connectPlatformId'];

        $parentUrl = $request->headers->get('referer');
        $callbackUrl = route('wechat-login.auth.callback', [
            'authUlid' => $authUlid,
            'connectPlatformId' => $connectPlatformId,
            'parentUrl' => $parentUrl,
            'langTag' => $langTag,
        ]);

        $oauthInfo = ConfigHelper::getWebOauthInfo($connectPlatformId, $authUlid, $callbackUrl, $langTag);
        $oauthUrl = $oauthInfo['oauthUrl'];
        $wechatQrCode = $oauthInfo['wechatQrCode'];

        if ($connectPlatformId == AccountConnect::CONNECT_WECHAT_WEBSITE_APPLICATION) {
            return \redirect($oauthUrl);
        }

        return view('WeChatLogin::sign-in', compact('authUlid', 'postMessageKey', 'oauthUrl', 'wechatQrCode'));
    }

    // 微信扫码后处理流程页面
    public function webSign(Request $request)
    {
        $authUlid = $request->authUlid;
        $langTag = $request->langTag;
        $code = $request->code;

        // 验证使用权限
        $cacheData = CacheHelper::get($authUlid, ConfigHelper::getAuthCacheTags());

        if (empty($cacheData)) {
            return view('WeChatLogin::error', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
            ]);
        }

        $isWeChat = LoginHelper::isWeChat();

        if (empty($code) && $isWeChat) {
            PluginCallback::updateOrCreate([
                'ulid' => $authUlid,
            ], [
                'plugin_fskey' => 'WeChatLogin',
                'type' => PluginCallback::TYPE_ACCOUNT,
                'content' => null,
                'is_used' => false,
            ]);

            $app = new Application(ConfigHelper::getConfig($cacheData['connectPlatformId']));
            $oauth = $app->getOauth();

            $oauthUrl = $oauth->redirect($request->fullUrl());

            return \redirect($oauthUrl);
        }

        // 有 accountId 表示为绑定，没有表示登录
        $accountId = $cacheData['accountId'] ?? null;

        if ($code && empty($accountId)) {
            $checkAccount = LoginHelper::checkAccount($cacheData['connectPlatformId'], $code, $langTag);

            if ($checkAccount['code']) {
                CacheHelper::put($checkAccount['data'], $authUlid, ConfigHelper::getAuthCacheTags(), null, now()->addMinutes(10));

                if ($checkAccount['code'] != 31502) {
                    return view('WeChatLogin::error', [
                        'code' => $checkAccount['code'],
                        'message' => $checkAccount['message'],
                    ]);
                }

                $wechatInfo = $checkAccount['data'];

                $createAccountUrl = route('wechat-login.create.account', [
                    'authUlid' => $authUlid,
                    'langTag' => $langTag,
                ]);

                return view('WeChatLogin::check-sign', compact('authUlid', 'wechatInfo', 'createAccountUrl'));
            } else {
                PluginCallback::updateOrCreate([
                    'ulid' => $authUlid,
                ], [
                    'plugin_fskey' => 'WeChatLogin',
                    'type' => PluginCallback::TYPE_ACCOUNT,
                    'content' => $checkAccount,
                    'is_used' => false,
                ]);
            }
        }

        if ($code && $accountId) {
            $connectAdd = LoginHelper::connectAdd($cacheData, AccountConnect::CONNECT_WECHAT_OFFICIAL_ACCOUNT, $code, $langTag);

            return view('WeChatLogin::error', [
                'code' => $connectAdd['code'],
                'message' => ConfigUtility::getCodeMessage($connectAdd['code'], 'Fresns', $langTag),
            ]);
        }

        return view('WeChatLogin::web-sign', compact('authUlid', 'code'));
    }

    // 授权后回调页面
    public function authCallback(Request $request)
    {
        $authUlid = $request->authUlid;
        $code = $request->code;
        $connectPlatformId = $request->connectPlatformId;
        $parentUrl = $request->parentUrl ?? FsConfigHelper::fresnsConfigByItemKey('site_url');
        $langTag = $request->langTag;

        if (empty($code) || empty($connectPlatformId)) {
            return view('WeChatLogin::error', [
                'code' => 30001,
                'message' => '[Code&ConnectPlatformId] '.ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        $checkAccount = LoginHelper::checkAccount($connectPlatformId, $code, $langTag);

        if ($checkAccount['code']) {
            CacheHelper::put($checkAccount['data'], $authUlid, ConfigHelper::getAuthCacheTags(), null, now()->addMinutes(10));

            if ($checkAccount['code'] != 31502) {
                return view('WeChatLogin::error', [
                    'code' => $checkAccount['code'],
                    'message' => $checkAccount['message'],
                ]);
            }

            $wechatInfo = $checkAccount['data'];

            $createAccountUrl = route('wechat-login.create.account', [
                'authUlid' => $authUlid,
                'parentUrl' => $parentUrl,
                'langTag' => $langTag,
            ]);

            return view('WeChatLogin::check-sign', compact('authUlid', 'wechatInfo', 'createAccountUrl'));
        }

        return \redirect($parentUrl);
    }

    // 授权后查询不到账号，创建新账号
    public function createAccount(Request $request)
    {
        $authUlid = $request->authUlid;
        $parentUrl = $request->parentUrl ?? FsConfigHelper::fresnsConfigByItemKey('site_url');
        $langTag = $request->langTag;

        // 验证使用权限
        $wechatInfo = CacheHelper::get($authUlid, ConfigHelper::getAuthCacheTags());

        if (empty($wechatInfo)) {
            return view('WeChatLogin::error', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
            ]);
        }

        if (empty($wechatInfo['openid'] ?? null)) {
            return view('WeChatLogin::error', [
                'code' => 30001,
                'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        $response = LoginHelper::createAccount($wechatInfo, $langTag);

        if ($response['code']) {
            return view('WeChatLogin::error', [
                'code' => $response['code'],
                'message' => $response['message'],
            ]);
        }

        $accountData = LoginHelper::getAccountData($response['data']['aid'], $langTag);

        PluginCallback::updateOrCreate([
            'ulid' => $authUlid,
        ], [
            'plugin_fskey' => 'WeChatLogin',
            'type' => PluginCallback::TYPE_ACCOUNT,
            'content' => $accountData,
            'is_used' => false,
        ]);

        return \redirect($parentUrl);
    }

    // 账号设置中绑定微信
    public function connectAdd(Request $request)
    {
        $authUlid = $request->authUlid;
        $langTag = $request->langTag;

        // 验证使用权限
        $cacheData = CacheHelper::get($authUlid, ConfigHelper::getAuthCacheTags());

        $postMessageKey = $cacheData['postMessageKey'] ?? '';

        if (empty($cacheData)) {
            return view('WeChatLogin::error', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
            ]);
        }

        // 验证是否登录状态
        if (empty($cacheData['accountId'] ?? null)) {
            return view('WeChatLogin::error', [
                'code' => 31501,
                'message' => ConfigUtility::getCodeMessage(31501, 'Fresns', $langTag),
            ]);
        }

        $parentUrl = $request->headers->get('referer');
        $callbackUrl = route('wechat-login.connect.add.callback', [
            'authUlid' => $authUlid,
            'langTag' => $langTag,
            'connectPlatformId' => $cacheData['connectPlatformId'],
        ]);
        $oauthInfo = ConfigHelper::getWebOauthInfo($cacheData['connectPlatformId'], $authUlid, $callbackUrl);

        $oauthUrl = $oauthInfo['oauthUrl'];
        $wechatQrCode = $oauthInfo['wechatQrCode'];

        if ($cacheData['connectPlatformId'] == AccountConnect::CONNECT_WECHAT_WEBSITE_APPLICATION) {
            return \redirect($oauthUrl);
        }

        return view('WeChatLogin::connect-add', compact('authUlid', 'oauthUrl', 'wechatQrCode', 'postMessageKey'));
    }

    // 账号设置中绑定微信: 回调流程
    public function connectAddCallback(Request $request)
    {
        $code = $request->code;
        $langTag = $request->langTag;
        $connectPlatformId = $request->connectPlatformId;

        if (empty($code)) {
            return view('WeChatLogin::error', [
                'code' => 30001,
                'message' => '[code] '.ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        $authUlid = $request->authUlid;

        // 验证使用权限
        $cacheData = CacheHelper::get($authUlid, ConfigHelper::getAuthCacheTags());

        if (empty($cacheData)) {
            return view('WeChatLogin::error', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
            ]);
        }

        // 验证是否登录状态
        if (empty($cacheData['accountId'] ?? null)) {
            return view('WeChatLogin::error', [
                'code' => 31501,
                'message' => ConfigUtility::getCodeMessage(31501, 'Fresns', $langTag),
            ]);
        }

        $connectAdd = LoginHelper::connectAdd($cacheData, $connectPlatformId, $code);

        if ($connectAdd['code'] == 0) {
            return \redirect('/account/settings#account-tab');
        }

        return view('WeChatLogin::error', [
            'code' => $connectAdd['code'],
            'message' => $connectAdd['message'],
        ]);
    }

    // 账号设置中移除绑定
    public function connectDisconnect(Request $request)
    {
        $authUlid = $request->authUlid;
        $connectPlatformId = $request->connectPlatformId;
        $langTag = $request->langTag;

        // 验证使用权限
        $cacheData = CacheHelper::get($authUlid, ConfigHelper::getAuthCacheTags());

        if (empty($cacheData)) {
            return view('WeChatLogin::error', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
            ]);
        }

        // 验证是否登录状态
        if (empty($cacheData['accountId'] ?? null)) {
            return view('WeChatLogin::error', [
                'code' => 31501,
                'message' => ConfigUtility::getCodeMessage(31501, 'Fresns', $langTag),
            ]);
        }

        $connectInfo = AccountConnect::where('account_id', $cacheData['accountId'])->where('connect_platform_id', $cacheData['connectPlatformId'])->first();

        return view('WeChatLogin::connect-disconnect', compact('authUlid', 'langTag', 'connectInfo'));
    }

    // 账号设置中移除绑定: 删除操作
    public function connectDisconnectResult(Request $request)
    {
        $authUlid = $request->authUlid;
        $langTag = $request->langTag;

        // 验证使用权限
        $cacheData = CacheHelper::get($authUlid, ConfigHelper::getAuthCacheTags());

        if (empty($cacheData)) {
            return view('WeChatLogin::error', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
            ]);
        }

        // 验证是否登录状态
        if (empty($cacheData['accountId'] ?? null)) {
            return view('WeChatLogin::error', [
                'code' => 31501,
                'message' => ConfigUtility::getCodeMessage(31501, 'Fresns', $langTag),
            ]);
        }

        $wordBody = [
            'aid' => $cacheData['aid'],
            'connectPlatformId' => $cacheData['connectPlatformId'],
        ];

        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->disconnectAccountConnect($wordBody);

        return view('WeChatLogin::error', [
            'code' => $fresnsResp->getCode(),
            'message' => $fresnsResp->getMessage(),
        ]);
    }
}
