<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\WeChatLogin\Http\Controllers;

use App\Exceptions\ApiException;
use App\Fresns\Api\Http\DTO\CommonCallbacksDTO;
use App\Fresns\Api\Traits\ApiResponseTrait;
use App\Helpers\CacheHelper;
use App\Helpers\PluginHelper;
use App\Models\AccountConnect;
use App\Models\Plugin;
use App\Models\PluginCallback;
use EasyWeChat\OfficialAccount\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Plugins\WeChatLogin\Helpers\ConfigHelper;
use Plugins\WeChatLogin\Helpers\LoginHelper;
use Plugins\WeChatLogin\Http\DTO\JsSdkSignDTO;
use Plugins\WeChatLogin\Http\DTO\OauthDTO;
use Plugins\WeChatLogin\Http\DTO\OauthWebsiteDTO;

class ApiController extends Controller
{
    use ApiResponseTrait;

    // 公共: 回调
    public function callback(Request $request)
    {
        $dtoRequest = new CommonCallbacksDTO($request->all());

        $callback = PluginHelper::fresnsPluginCallback($dtoRequest->fskey, $dtoRequest->ulid);

        if ($callback['code']) {
            throw new ApiException($callback['code']);
        }

        return $this->success($callback['data']);
    }

    // 公共: 重置回调
    public function recallback(Request $request)
    {
        $dtoRequest = new CommonCallbacksDTO($request->all());

        $plugin = Plugin::where('fskey', $dtoRequest->fskey)->first();

        if (empty($plugin)) {
            throw new ApiException(32101);
        }

        if (! $plugin->is_enabled) {
            throw new ApiException(32102);
        }

        $callback = PluginCallback::where('ulid', $dtoRequest->ulid)->first();

        if (empty($callback)) {
            throw new ApiException(32303);
        }

        $callback->update([
            'is_used' => true,
        ]);

        return $this->success();
    }

    // 小程序登录
    public function miniProgramOauth(Request $request)
    {
        $dtoRequest = new OauthDTO($request->all());
        $aid = \request()->header('X-Fresns-Aid');
        $appId = \request()->header('X-Fresns-App-Id');
        $platformId = \request()->header('X-Fresns-Client-Platform-Id');
        $version = \request()->header('X-Fresns-Client-Version');
        $langTag = \request()->header('X-Fresns-Client-Lang-Tag');

        // 有 aid 表示为绑定关联
        if ($aid) {
            $cacheData['aid'] = $aid;
            $connectAdd = LoginHelper::connectAdd($cacheData, AccountConnect::CONNECT_WECHAT_MINI_PROGRAM, $dtoRequest->code, $langTag);

            if ($connectAdd['code']) {
                return $connectAdd;
            }

            $accountData = LoginHelper::getAccountData($aid, $langTag, $appId, $platformId, $version);

            return $accountData;
        }

        // 没有 aid 表示登录或注册
        $checkAccount = LoginHelper::checkAccount(AccountConnect::CONNECT_WECHAT_MINI_PROGRAM, $dtoRequest->code, $langTag, $appId, $platformId, $version);

        if (! $dtoRequest->autoRegister) {
            return $checkAccount;
        }

        $checkAccount['data']['nickname'] = $dtoRequest->nickname ?? $checkAccount['data']['nickname'];
        $checkAccount['data']['avatarUrl'] = $dtoRequest->avatarUrl ?? $checkAccount['data']['avatarUrl'];

        if ($checkAccount['code'] == 31502) {
            $response = LoginHelper::createAccount($checkAccount['data'], $langTag, $appId, $platformId, $version);

            if ($response['code']) {
                return $response;
            }

            $accountData = LoginHelper::getAccountData($response['data']['aid'], $langTag, $appId, $platformId, $version);

            return $accountData;
        }

        return $checkAccount;
    }

    // 小程序授权网页登录
    public function miniProgramOauthWebsite(Request $request)
    {
        $dtoRequest = new OauthWebsiteDTO($request->all());
        $langTag = \request()->header('X-Fresns-Client-Lang-Tag');

        // 本接口是授权网页登录，所以不需要 headers 里的 $appId, $platformId, $version 参数
        // 因为 headers 参数是小程序平台，但生成的凭证要给网页用的，所以不能用小程序 headers

        // 验证使用权限
        $cacheData = CacheHelper::get($dtoRequest->ulid, ConfigHelper::getAuthCacheTags());

        if (empty($cacheData)) {
            return $this->failure(32203);
        }

        PluginCallback::updateOrCreate([
            'ulid' => $dtoRequest->ulid,
        ], [
            'plugin_fskey' => 'WeChatLogin',
            'type' => PluginCallback::TYPE_ACCOUNT,
            'content' => null,
            'is_used' => false,
        ]);

        // 有 aid 表示为绑定关联
        if ($cacheData['aid'] ?? null) {
            $connectAdd = LoginHelper::connectAdd($cacheData, AccountConnect::CONNECT_WECHAT_MINI_PROGRAM, $dtoRequest->code, $langTag);

            if ($connectAdd['code']) {
                return $connectAdd;
            }

            $accountData = LoginHelper::getAccountData($cacheData['aid'], $langTag);

            PluginCallback::updateOrCreate([
                'ulid' => $dtoRequest->ulid,
            ], [
                'plugin_fskey' => 'WeChatLogin',
                'type' => PluginCallback::TYPE_ACCOUNT,
                'content' => $accountData,
                'is_used' => false,
            ]);

            return $accountData;
        }

        // 没有 aid 表示登录或注册
        $checkAccount = LoginHelper::checkAccount(AccountConnect::CONNECT_WECHAT_MINI_PROGRAM, $dtoRequest->code, $langTag);

        if ($checkAccount['code'] == 0) {
            PluginCallback::updateOrCreate([
                'ulid' => $dtoRequest->ulid,
            ], [
                'plugin_fskey' => 'WeChatLogin',
                'type' => PluginCallback::TYPE_ACCOUNT,
                'content' => $checkAccount,
                'is_used' => false,
            ]);
        }

        if (! $dtoRequest->autoRegister) {
            return $checkAccount;
        }

        if ($checkAccount['code'] == 31502) {
            $response = LoginHelper::createAccount($checkAccount['data'], $langTag);

            if ($response['code']) {
                return $response;
            }

            $accountData = LoginHelper::getAccountData($response['data']['aid'], $langTag);

            PluginCallback::updateOrCreate([
                'ulid' => $dtoRequest->ulid,
            ], [
                'plugin_fskey' => 'WeChatLogin',
                'type' => PluginCallback::TYPE_ACCOUNT,
                'content' => $accountData,
                'is_used' => false,
            ]);

            return $accountData;
        }

        return $checkAccount;
    }

    // 移动应用登录
    public function openPlatformOauth(Request $request)
    {
        $dtoRequest = new OauthDTO($request->all());
        $aid = \request()->header('X-Fresns-Aid');
        $appId = \request()->header('X-Fresns-App-Id');
        $platformId = \request()->header('X-Fresns-Client-Platform-Id');
        $version = \request()->header('X-Fresns-Client-Version');
        $langTag = \request()->header('X-Fresns-Client-Lang-Tag');

        // 有 aid 表示为绑定关联
        if ($aid) {
            $cacheData['aid'] = $aid;
            $connectAdd = LoginHelper::connectAdd($cacheData, AccountConnect::CONNECT_WECHAT_MOBILE_APPLICATION, $dtoRequest->code, $langTag);

            if ($connectAdd['code']) {
                return $connectAdd;
            }

            $accountData = LoginHelper::getAccountData($aid, $langTag, $appId, $platformId, $version);

            return $accountData;
        }

        // 没有 aid 表示登录或注册
        $checkAccount = LoginHelper::checkAccount(AccountConnect::CONNECT_WECHAT_MOBILE_APPLICATION, $dtoRequest->code, $langTag, $appId, $platformId, $version);

        if (! $dtoRequest->autoRegister) {
            return $checkAccount;
        }

        if ($checkAccount['code'] == 31502) {
            $response = LoginHelper::createAccount($checkAccount['data'], $langTag, $appId, $platformId, $version);

            if ($response['code']) {
                return $response;
            }

            $accountData = LoginHelper::getAccountData($response['data']['aid'], $langTag, $appId, $platformId, $version);

            return $accountData;
        }

        return $checkAccount;
    }

    // 多端应用 Apple 登录
    public function miniAppOauthApple(Request $request)
    {
        $dtoRequest = new OauthDTO($request->all());
        $aid = \request()->header('X-Fresns-Aid');
        $appId = \request()->header('X-Fresns-App-Id');
        $platformId = \request()->header('X-Fresns-Client-Platform-Id');
        $version = \request()->header('X-Fresns-Client-Version');
        $langTag = \request()->header('X-Fresns-Client-Lang-Tag');

        // 有 aid 表示为绑定关联
        if ($aid) {
            $cacheData['aid'] = $aid;
            $connectAdd = LoginHelper::connectAdd($cacheData, AccountConnect::CONNECT_APPLE, $dtoRequest->code, $langTag);

            if ($connectAdd['code']) {
                return $connectAdd;
            }

            $accountData = LoginHelper::getAccountData($aid, $langTag, $appId, $platformId, $version);

            return $accountData;
        }

        // 没有 aid 表示登录或注册
        $checkAccount = LoginHelper::checkAccount(AccountConnect::CONNECT_APPLE, $dtoRequest->code, $langTag, $appId, $platformId, $version);

        if (! $dtoRequest->autoRegister) {
            return $checkAccount;
        }

        if ($checkAccount['code'] == 31502) {
            $response = LoginHelper::createAccount($checkAccount['data'], $langTag, $appId, $platformId, $version);

            if ($response['code']) {
                return $response;
            }

            $accountData = LoginHelper::getAccountData($response['data']['aid'], $langTag, $appId, $platformId, $version);

            return $accountData;
        }

        return $checkAccount;
    }

    // 获取 JS-SDK 签名
    public function jsSdkSign(Request $request)
    {
        $dtoRequest = new JsSdkSignDTO($request->all());
        $url = $dtoRequest->url;

        $hashPosition = strpos($url, '#');
        if ($hashPosition !== false) {
            $url = substr($url, 0, $hashPosition);
        }

        $cacheKey = 'wechatlogin_js_sdk_'.md5($url);
        $cacheTags = ConfigHelper::getAuthCacheTags();
        $config = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($config)) {
            $app = new Application(ConfigHelper::getConfig(AccountConnect::CONNECT_WECHAT_OFFICIAL_ACCOUNT));

            $utils = $app->getUtils();

            $config = $utils->buildJsSdkConfig(
                debug: false,
                url: $url,
                jsApiList: ['updateAppMessageShareData', 'updateTimelineShareData'],
                openTagList: ['wx-open-launch-weapp', 'wx-open-launch-app', 'wx-open-subscribe', 'wx-open-audio'],
            );

            CacheHelper::put($config, $cacheKey, $cacheTags, 110);
        }

        return [
            'code' => 0,
            'message' => 'ok',
            'data' => $config,
        ];
    }
}
