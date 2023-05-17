<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\WeChatLogin\Helpers;

use App\Helpers\ConfigHelper as FsConfigHelper;
use App\Models\AccountConnect;
use EasyWeChat\MiniApp\Application as MiniApp;
use EasyWeChat\OfficialAccount\Application;
use EasyWeChat\OpenPlatform\Application as OpenPlatform;

class ConfigHelper
{
    // 获取本插件的缓存标签
    public static function getAuthCacheTags(): array
    {
        return ['fresnsPlugins', 'pluginWeChatLogin', 'fresnsPluginAuth'];
    }

    // 获取配置信息
    public static function getConfig(int $connectId): array
    {
        $configArr = match ($connectId) {
            AccountConnect::CONNECT_WECHAT_OFFICIAL_ACCOUNT => FsConfigHelper::fresnsConfigByItemKey('wechatlogin_official_account'),
            AccountConnect::CONNECT_WECHAT_MINI_PROGRAM => FsConfigHelper::fresnsConfigByItemKey('wechatlogin_mini_program'),
            AccountConnect::CONNECT_WECHAT_MOBILE_APPLICATION => FsConfigHelper::fresnsConfigByItemKey('wechatlogin_open_platform')['mobile'],
            AccountConnect::CONNECT_WECHAT_WEBSITE_APPLICATION => FsConfigHelper::fresnsConfigByItemKey('wechatlogin_open_platform')['website'],
            default => FsConfigHelper::fresnsConfigByItemKey('wechatlogin_official_account'),
        };

        // $configArr = [
        //     'appId' => '',
        //     'appSecret' => '',
        // ];

        $appId = $configArr['appId'] ?? null;
        $appSecret = $configArr['appSecret'] ?? null;

        $scopes = match ($connectId) {
            AccountConnect::CONNECT_WECHAT_OFFICIAL_ACCOUNT => ['snsapi_userinfo'],
            AccountConnect::CONNECT_WECHAT_MINI_PROGRAM => ['snsapi_userinfo'],
            AccountConnect::CONNECT_WECHAT_MOBILE_APPLICATION => ['snsapi_login'],
            AccountConnect::CONNECT_WECHAT_WEBSITE_APPLICATION => ['snsapi_login'],
            default => ['snsapi_userinfo'],
        };

        $config = [
            /**
             * 账号基本信息，请从微信公众平台/开放平台获取
             */
            'app_id'  => $appId,        // AppID
            'secret'  => $appSecret,    // AppSecret
            'token'   => '',            // Token
            'aes_key' => '',            // EncodingAESKey，兼容与安全模式下请一定要填写！！！

            /**
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址
             */
            'oauth' => [
                'scopes'   => $scopes,
                'redirect_url' => '',
            ],

            /**
             * 接口请求相关配置，超时时间等，具体可用参数请参考：
             * https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
             */
            'http' => [
                'timeout' => 5.0,
                // 'base_uri' => 'https://api.weixin.qq.com/', // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri

                'retry' => true, // 使用默认重试配置
                //  'retry' => [
                //      // 仅以下状态码重试
                //      'http_codes' => [429, 500]
                //       // 最大重试次数
                //      'max_retries' => 3,
                //      // 请求间隔 (毫秒)
                //      'delay' => 1000,
                //      // 如果设置，每次重试的等待时间都会增加这个系数
                //      // (例如. 首次:1000ms; 第二次: 3 * 1000ms; etc.)
                //      'multiplier' => 3
                //  ],
            ],
        ];

        return $config;
    }

    // 获取网页登录信息
    public static function getWebOauthInfo(int $connectId, string $authUlid, string $callbackUrl, ?string $langTag = null): array
    {
        $isWeChat = LoginHelper::isWeChat();
        $wechatQrCode = null;
        $oauthUrl = null;

        $app = new Application(ConfigHelper::getConfig($connectId));

        switch ($connectId) {
            case AccountConnect::CONNECT_WECHAT_OFFICIAL_ACCOUNT:
                // 公众号
                if ($isWeChat) {
                    $oauth = $app->getOauth();

                    $oauthUrl = $oauth->redirect($callbackUrl);
                } else {
                    $wechatQrCode = LoginHelper::getQrCode($authUlid, $langTag);
                }
                break;

            case AccountConnect::CONNECT_WECHAT_MINI_PROGRAM:
                // 小程序授权网页登录
                $miniProgramConfig = FsConfigHelper::fresnsConfigByItemKey('wechatlogin_mini_program');

                $response = $app->getClient()->postJson('/wxa/getwxacodeunlimit', [
                    'scene' => $authUlid,
                    'page' => 'pages/account/wechat-login/website-oauth',
                    'check_path' => false,
                    'env_version' => $miniProgramConfig['envVersion'] ?? 'release',
                ]);

                $wechatQrCode = $response->toDataUrl();
                break;

            case AccountConnect::CONNECT_WECHAT_WEBSITE_APPLICATION:
                // 开放平台-网站应用
                $oauth = $app->getOauth();

                $oauthUrl = $oauth->redirect($callbackUrl);
                break;

            default:
                // 互联 ID 不符合要求
                break;
        }

        return [
            'oauthUrl' => $oauthUrl,
            'wechatQrCode' => $wechatQrCode,
        ];
    }

    // 获取微信授权信息
    public static function getWeChatUserInfo(int $connectId, string $code): array
    {
        switch ($connectId) {
            case AccountConnect::CONNECT_WECHAT_OFFICIAL_ACCOUNT:
                // 公众号
                $app = new Application(ConfigHelper::getConfig($connectId));

                $oauth = $app->getOauth();

                try {
                    $user = $oauth->userFromCode($code);
                } catch (\Exception $e) {
                    return [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage(),
                        'data' => null,
                    ];
                }

                $rawInfo = $user->getRaw();

                $wechatConfig = [
                    'unionid' => $rawInfo['unionid'] ?? null,
                    'openid' => $user->getId(),
                    'refreshToken' => $user->getRefreshToken(),
                    'refreshTokenExpiredDatetime' => now()->addDays(30)->format('Y-m-d H:i:s'),
                    'nickname' => $user->getNickname(),
                    'avatarUrl' => $user->getAvatar(),
                ];
                break;

            case AccountConnect::CONNECT_WECHAT_MINI_PROGRAM:
                // 小程序
                $app = new MiniApp(ConfigHelper::getConfig($connectId));
                $utils = $app->getUtils();

                try {
                    $response = $utils->codeToSession($code);
                } catch (\Exception $e) {
                    return [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage(),
                        'data' => null,
                    ];
                }

                $wechatConfig = [
                    'unionid' => $response['unionid'] ?? null,
                    'openid' => $response['openid'],
                    'refreshToken' => null,
                    'refreshTokenExpiredDatetime' => null,
                    'nickname' => null,
                    'avatarUrl' => null,
                ];
                break;

            case AccountConnect::CONNECT_WECHAT_MOBILE_APPLICATION:
                // 开放平台-移动应用
                $app = new OpenPlatform(ConfigHelper::getConfig($connectId));

                try {
                    $api = $app->getClient();

                    $opConfig = FsConfigHelper::fresnsConfigByItemKey('wechatlogin_open_platform');

                    $response = $api->get('/sns/oauth2/access_token', [
                        'appid' => $opConfig['mobile']['appId'],
                        'secret' => $opConfig['mobile']['appSecret'],
                        'code' => $code,
                        'grant_type' => 'authorization_code',
                    ]);
                } catch (\Exception $e) {
                    return [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage(),
                        'data' => null,
                    ];
                }

                if ($response->isFailed()) {
                    return [
                        'code' => 32302,
                        'message' => $response->getContent(),
                        'data' => null,
                    ];
                }

                $resData = $response->toArray();

                $wechatConfig = [
                    'unionid' => $resData['unionid'] ?? null,
                    'openid' => $resData['openid'],
                    'refreshToken' => $resData['refresh_token'],
                    'refreshTokenExpiredDatetime' => now()->addDays(30)->format('Y-m-d H:i:s'),
                    'nickname' => null,
                    'avatarUrl' => null,
                ];
                break;

            case AccountConnect::CONNECT_WECHAT_WEBSITE_APPLICATION:
                // 开放平台-网站应用
                $app = new OpenPlatform(ConfigHelper::getConfig($connectId));
                $oauth = $app->getOauth();

                try {
                    $user = $oauth->userFromCode($code);
                } catch (\Exception $e) {
                    return [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage(),
                        'data' => null,
                    ];
                }

                $rawInfo = $user->getRaw();

                $wechatConfig = [
                    'unionid' => $rawInfo['unionid'] ?? null,
                    'openid' => $user->getId(),
                    'refreshToken' => $user->getRefreshToken(),
                    'refreshTokenExpiredDatetime' => now()->addDays(30)->format('Y-m-d H:i:s'),
                    'nickname' => $user->getNickname(),
                    'avatarUrl' => $user->getAvatar(),
                ];
                break;

            default:
                // 互联 ID 不符合要求
                return [
                    'code' => 30002,
                    'message' => 'ok',
                    'data' => null,
                ];
                break;
        }

        return [
            'code' => 0,
            'message' => 'ok',
            'data' => $wechatConfig,
        ];
    }
}
