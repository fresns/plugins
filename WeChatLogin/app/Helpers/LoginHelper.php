<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\WeChatLogin\Helpers;

use App\Helpers\AppHelper;
use App\Helpers\ConfigHelper as FsConfigHelper;
use App\Helpers\PluginHelper;
use App\Helpers\PrimaryHelper;
use App\Models\Account;
use App\Models\AccountConnect;
use App\Models\PluginCallback;
use App\Models\SessionLog;
use App\Utilities\ConfigUtility;
use Browser;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class LoginHelper
{
    // 判断是否微信中访问
    public static function isWeChat(): bool
    {
        $agent = Browser::userAgent();
        $result = Str::of($agent)->lower();

        $contains = Str::contains($result, 'micromessenger');

        return $contains;
    }

    // 获取扫码授权的二维码
    public static function getQrCode(string $authUlid, ?string $langTag = null): string
    {
        $langTag = $langTag ?: FsConfigHelper::fresnsConfigDefaultLangTag();

        $signUrl = route('wechat-login.web.sign', [
            'authUlid' => $authUlid,
            'langTag' => $langTag,
        ]);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($signUrl)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->validateResult(false)
            ->build();

        return $result->getDataUri();
    }

    // 判断账号是否存在
    public static function checkAccount(int $connectPlatformId, string $code, ?string $langTag = null, ?string $appId = null, ?int $platformId = null, ?string $version = null): array
    {
        $langTag = $langTag ?: FsConfigHelper::fresnsConfigDefaultLangTag();

        $wechatInfo = ConfigHelper::getWeChatUserInfo($connectPlatformId, $code);

        if ($wechatInfo['code']) {
            return $wechatInfo;
        }

        $wechatConfig = $wechatInfo['data'];

        // 查询 unionid 是否有账号
        $unionAid = null;
        if ($wechatConfig['unionid']) {
            $unionIdWordBody = [
                'type' => Account::ACT_TYPE_CONNECT,
                'connectPlatformId' => AccountConnect::CONNECT_WECHAT_OPEN_PLATFORM,
                'connectAccountId' => $wechatConfig['unionid'],
            ];

            $unionIdResp = \FresnsCmdWord::plugin('Fresns')->verifyAccount($unionIdWordBody);

            $unionAid = $unionIdResp->getData('aid') ?? null;
        }

        // 查询 openid 是否有账号
        $openIdWordBody = [
            'type' => Account::ACT_TYPE_CONNECT,
            'connectPlatformId' => $connectPlatformId,
            'connectAccountId' => $wechatConfig['openid'],
        ];

        $openIdResp = \FresnsCmdWord::plugin('Fresns')->verifyAccount($openIdWordBody);

        // 合并计算 unionid 和 openid 是否有账号
        $aid = $unionAid ?? $openIdResp->getData('aid') ?? null;

        if (empty($aid)) {
            $noAccount = [
                'code' => 31502,
                'message' => ConfigUtility::getCodeMessage(31502, 'Fresns', $langTag),
                'data' => $wechatConfig,
            ];

            return $noAccount;
        }

        $unionWordBody = [
            'fskey' => 'WeChatLogin',
            'aid' => $aid,
            'connectPlatformId' => AccountConnect::CONNECT_WECHAT_OPEN_PLATFORM,
            'connectAccountId' => $wechatConfig['unionid'],
            'connectNickname' => $wechatConfig['nickname'],
            'connectAvatar' => $wechatConfig['avatarUrl'],
        ];

        \FresnsCmdWord::plugin('Fresns')->setAccountConnect($unionWordBody);

        $openWordBody = [
            'fskey' => 'WeChatLogin',
            'aid' => $aid,
            'connectPlatformId' => $connectPlatformId,
            'connectAccountId' => $wechatConfig['openid'],
            'connectRefreshToken' => $wechatConfig['refreshToken'],
            'refreshTokenExpiredDatetime' => $wechatConfig['refreshTokenExpiredDatetime'],
            'connectNickname' => $wechatConfig['nickname'],
            'connectAvatar' => $wechatConfig['avatarUrl'],
        ];

        \FresnsCmdWord::plugin('Fresns')->setAccountConnect($openWordBody);

        return LoginHelper::getAccountData($aid, $langTag, $appId, $platformId, $version);
    }

    // 获取账号数据
    public static function getAccountData(string $aid, ?string $langTag = null, ?string $appId = null, ?int $platformId = null, ?string $version = null): array
    {
        $langTag = $langTag ?: FsConfigHelper::fresnsConfigDefaultLangTag();

        // 获取账号详情
        $wordBody = [
            'aid' => $aid,
            'langTag' => $langTag,
        ];
        $accountDetail = \FresnsCmdWord::plugin('Fresns')->getAccountDetail($wordBody);

        if ($accountDetail->isErrorResponse()) {
            return $accountDetail->getOrigin();
        }

        // 创建账号凭证
        $keyId = FsConfigHelper::fresnsConfigByItemKey('engine_key_id');
        $engineVersion = PluginHelper::fresnsPluginVersionByFskey('FresnsEngine');
        $keyInfo = PrimaryHelper::fresnsModelById('key', $keyId);

        $createTokenWordBody = [
            'platformId' => $platformId ?? 4,
            'version' => $version ?? $engineVersion ?? AppHelper::VERSION,
            'appId' => $appId ?? $keyInfo?->app_id,
            'aid' => $aid,
            'expiredTime' => null,
        ];
        $fresnsTokenResponse = \FresnsCmdWord::plugin('Fresns')->createAccountToken($createTokenWordBody);

        if ($fresnsTokenResponse->isErrorResponse()) {
            return $fresnsTokenResponse->getOrigin();
        }

        $data = [
            'sessionToken' => [
                'aid' => $fresnsTokenResponse->getData('aid'),
                'token' => $fresnsTokenResponse->getData('aidToken'),
                'expiredHours' => $fresnsTokenResponse->getData('expiredHours'),
                'expiredDays' => $fresnsTokenResponse->getData('expiredDays'),
                'expiredDateTime' => $fresnsTokenResponse->getData('expiredDateTime'),
            ],
            'detail' => $accountDetail->getData(),
        ];

        // 将账号凭证写入 Cookies
        if (empty($platformId) || in_array($platformId, [2, 3, 4])) {
            $cookiePrefix = FsConfigHelper::fresnsConfigByItemKey('engine_cookie_prefix') ?? 'fresns_';
            $fresnsAid = "{$cookiePrefix}aid";
            $fresnsAidToken = "{$cookiePrefix}aid_token";

            Cookie::queue($fresnsAid, $data['sessionToken']['aid'], 525600);
            Cookie::queue($fresnsAidToken, $data['sessionToken']['token'], 525600);
        }

        $accountData = [
            'code' => 0,
            'message' => ConfigUtility::getCodeMessage(0, 'Fresns', $langTag),
            'data' => $data,
        ];

        return $accountData;
    }

    // 创建账号
    public static function createAccount(array $wechatConfig, ?string $langTag = null, ?string $appId = null, ?int $platformId = null, ?string $version = null): array
    {
        $connectInfo = [
            [
                'connectPlatformId' => $wechatConfig['connectPlatformId'],
                'connectAccountId' => $wechatConfig['openid'],
                'connectRefreshToken' => $wechatConfig['refreshToken'],
                'refreshTokenExpiredDatetime' => $wechatConfig['refreshTokenExpiredDatetime'],
                'connectNickname' => $wechatConfig['nickname'],
                'connectAvatar' => $wechatConfig['avatarUrl'],
                'pluginFskey' => 'WeChatLogin',
            ],
        ];

        if ($wechatConfig['unionid']) {
            $unionArr = [
                'connectPlatformId' => AccountConnect::CONNECT_WECHAT_OPEN_PLATFORM,
                'connectAccountId' => $wechatConfig['unionid'],
                'connectNickname' => $wechatConfig['nickname'],
                'connectAvatar' => $wechatConfig['avatarUrl'],
                'pluginFskey' => 'WeChatLogin',
            ];

            $connectInfo[] = $unionArr;
        }

        // create account
        $createAccountWordBody = [
            'type' => Account::ACT_TYPE_CONNECT,
            'account' => '',
            'connectInfo' => $connectInfo,
            'createUser' => true,
            'userInfo' => [
                'nickname' => $wechatConfig['nickname'],
                'avatarUrl' => $wechatConfig['avatarUrl'],
            ],
        ];

        $createAccountResp = \FresnsCmdWord::plugin('Fresns')->createAccount($createAccountWordBody);

        $keyId = FsConfigHelper::fresnsConfigByItemKey('engine_key_id');
        $engineVersion = PluginHelper::fresnsPluginVersionByFskey('FresnsEngine');
        $keyInfo = PrimaryHelper::fresnsModelById('key', $keyId);

        // session log
        $sessionLog = [
            'type' => SessionLog::TYPE_ACCOUNT_REGISTER,
            'fskey' => 'Fresns',
            'appId' => $appId ?? $keyInfo?->app_id,
            'platformId' => $platformId ?? 4,
            'version' => $version ?? $engineVersion ?? AppHelper::VERSION,
            'langTag' => $langTag ?: FsConfigHelper::fresnsConfigDefaultLangTag(),
            'aid' => null,
            'uid' => null,
            'objectName' => \request()->path(),
            'objectAction' => 'Account Register',
            'objectResult' => SessionLog::STATE_SUCCESS,
            'objectOrderId' => null,
            'deviceInfo' => AppHelper::getDeviceInfo(),
            'deviceToken' => null,
            'moreJson' => null,
        ];

        if ($createAccountResp->isErrorResponse()) {
            // upload session log
            $sessionLog['objectResult'] = SessionLog::STATE_FAILURE;

            \FresnsCmdWord::plugin('Fresns')->uploadSessionLog($sessionLog);

            return $createAccountResp->getOrigin();
        }

        $sessionLog['aid'] = $createAccountResp->getData('aid');
        $sessionLog['uid'] = $createAccountResp->getData('uid');
        $sessionLog['objectAction'] = '[WeChatLogin] create account';
        \FresnsCmdWord::plugin('Fresns')->uploadSessionLog($sessionLog);

        return $createAccountResp->getOrigin();
    }

    // 绑定关联
    public static function connectAdd(array $cacheData, int $connectPlatformId, string $code, ?string $langTag = null): array
    {
        $langTag = $langTag ?: FsConfigHelper::fresnsConfigDefaultLangTag();

        $wechatInfo = ConfigHelper::getWeChatUserInfo($connectPlatformId, $code);

        if ($wechatInfo['code']) {
            return $wechatInfo;
        }

        $wechatConfig = $wechatInfo['data'];

        if ($wechatConfig['unionid']) {
            $unionWordBody = [
                'fskey' => 'WeChatLogin',
                'aid' => $cacheData['aid'],
                'connectPlatformId' => AccountConnect::CONNECT_WECHAT_OPEN_PLATFORM,
                'connectAccountId' => $wechatConfig['unionid'],
                'connectNickname' => $wechatConfig['nickname'],
                'connectAvatar' => $wechatConfig['avatarUrl'],
            ];

            $unionResp = \FresnsCmdWord::plugin('Fresns')->setAccountConnect($unionWordBody);

            // unionid 已经存在，并且不是当前账号的，中止流程
            if ($unionResp->getCode() == 34405) {
                return $unionResp->getOrigin();
            }
        }

        $openWordBody = [
            'fskey' => 'WeChatLogin',
            'aid' => $cacheData['aid'],
            'connectPlatformId' => $connectPlatformId,
            'connectAccountId' => $wechatConfig['openid'],
            'connectRefreshToken' => $wechatConfig['refreshToken'],
            'refreshTokenExpiredDatetime' => $wechatConfig['refreshTokenExpiredDatetime'],
            'connectNickname' => $wechatConfig['nickname'],
            'connectAvatar' => $wechatConfig['avatarUrl'],
        ];

        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->setAccountConnect($openWordBody);

        if ($cacheData['ulid'] ?? null) {
            PluginCallback::updateOrCreate([
                'ulid' => $cacheData['ulid'],
            ], [
                'plugin_fskey' => 'WeChatLogin',
                'type' => PluginCallback::TYPE_ACCOUNT,
                'content' => [
                    'code' => 0,
                    'message' => 'ok',
                    'data' => $cacheData,
                ],
                'is_use' => false,
            ]);
        }

        return $fresnsResp->getOrigin();
    }
}
