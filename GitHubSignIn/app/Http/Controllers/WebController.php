<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\GitHubSignIn\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\PrimaryHelper;
use App\Helpers\SignHelper as AppSignHelper;
use App\Models\Account;
use App\Models\AccountConnect;
use App\Models\SessionLog;
use App\Utilities\ConfigUtility;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Plugins\GitHubSignIn\Helpers\SignHelper;

class WebController extends Controller
{
    public function index(Request $request)
    {
        // Connect Platform ID
        // If your plugin supports multiple platforms, you can differentiate handling based on the platform ID.
        $connectPlatformId = $request->connectPlatformId;

        // verify access token
        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->verifyAccessToken([
            'accessToken' => $request->accessToken,
        ]);

        if ($fresnsResp->isErrorResponse()) {
            return view('GitHubSignIn::tips', [
                'code' => $fresnsResp->getCode(),
                'message' => $fresnsResp->getMessage(),
                'data' => [
                    'redirectURL' => null,
                ],
            ]);
        }

        // Redirect URL
        $redirectURL = $request->redirectURL;
        if ($redirectURL) {
            $redirectURL = urldecode($redirectURL);

            if ($redirectURL == '{redirectUrl}') {
                $redirectURL = null;
            }
        }

        // auth data
        $authUlid = (string) Str::ulid();
        $langTag = $fresnsResp->getData('langTag') ?? AppHelper::getLangTag();

        $accountId = PrimaryHelper::fresnsPrimaryId('account', $fresnsResp->getData('aid'));

        $cacheData = [
            'ulid' => $authUlid,
            'headers' => $fresnsResp->getData(),
            'accountId' => $accountId,
            'connectPlatformId' => $connectPlatformId,
            'redirectURL' => urldecode($redirectURL),
        ];

        CacheHelper::put($cacheData, $authUlid, 'fresnsPluginAuth', 10);

        // Logged in, proceed to the operation page.
        if ($accountId) {
            $connectInfo = AccountConnect::where('account_id', $accountId)->where('connect_platform_id', $connectPlatformId)->first();

            // Already connected, perform disconnect.
            if ($connectInfo) {
                return redirect()->to(route('github-signin.connect.disconnect', [
                    'authUlid' => $authUlid,
                    'langTag' => $langTag,
                ]));
            }

            // Not connected, perform disconnect.
            return redirect()->to(route('github-signin.connect.add', [
                'authUlid' => $authUlid,
                'langTag' => $langTag,
            ]));
        }

        // Not logged in, authorize login.
        return redirect()->to(route('github-signin.connect.sign-in', [
            'authUlid' => $authUlid,
            'langTag' => $langTag,
        ]));
    }

    // github-signin.connect.sign-in
    public function signIn(Request $request)
    {
        $authUlid = $request->authUlid;
        $langTag = $request->langTag ?? AppHelper::getLangTag();

        // Verify usage permissions.
        $cacheData = CacheHelper::get($authUlid, 'fresnsPluginAuth');
        if (empty($cacheData)) {
            return view('GitHubSignIn::tips', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
                'data' => [
                    'redirectURL' => null,
                ],
            ]);
        }

        // If your plugin supports multiple platforms, you can differentiate handling based on the platform ID.
        $connectPlatformId = $cacheData['connectPlatformId'];

        $fresnsLang = ConfigHelper::fresnsConfigLanguagePack($langTag);

        $callbackUrl = route('github-signin.auth.callback', [
            'authUlid' => $authUlid,
            'langTag' => $langTag,
        ]);

        SignHelper::setDriverConfig($callbackUrl);
        $oauthUrl = Socialite::driver('github')->redirect()->getTargetUrl();

        return view('GitHubSignIn::connect.sign-in', compact('fresnsLang', 'oauthUrl'));
    }

    // github-signin.connect.add
    public function add(Request $request)
    {
        $authUlid = $request->authUlid;
        $langTag = $request->langTag ?? AppHelper::getLangTag();

        // Verify usage permissions.
        $cacheData = CacheHelper::get($authUlid, 'fresnsPluginAuth');
        if (empty($cacheData)) {
            return view('GitHubSignIn::tips', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
                'data' => [
                    'redirectURL' => null,
                ],
            ]);
        }

        // Check sign in
        if (empty($cacheData['accountId'])) {
            return view('GitHubSignIn::tips', [
                'code' => 31501,
                'message' => ConfigUtility::getCodeMessage(31501, 'Fresns', $langTag),
                'data' => [
                    'redirectURL' => $cacheData['redirectURL'],
                ],
            ]);
        }

        // If your plugin supports multiple platforms, you can differentiate handling based on the platform ID.
        $connectPlatformId = $cacheData['connectPlatformId'];

        $fresnsLang = ConfigHelper::fresnsConfigLanguagePack($langTag);

        $callbackUrl = route('github-signin.auth.callback', [
            'authUlid' => $authUlid,
            'langTag' => $langTag,
        ]);

        SignHelper::setDriverConfig($callbackUrl);
        $oauthUrl = Socialite::driver('github')->redirect()->getTargetUrl();

        return view('GitHubSignIn::connect.add', compact('fresnsLang', 'oauthUrl'));
    }

    // github-signin.connect.disconnect
    public function disconnect(Request $request)
    {
        $authUlid = $request->authUlid;
        $langTag = $request->langTag ?? AppHelper::getLangTag();

        // Verify usage permissions.
        $cacheData = CacheHelper::get($authUlid, 'fresnsPluginAuth');
        if (empty($cacheData)) {
            return view('GitHubSignIn::tips', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
                'data' => [
                    'redirectURL' => null,
                ],
            ]);
        }

        // Check sign in
        if (empty($cacheData['accountId'])) {
            return view('GitHubSignIn::tips', [
                'code' => 31501,
                'message' => ConfigUtility::getCodeMessage(31501, 'Fresns', $langTag),
                'data' => [
                    'redirectURL' => $cacheData['redirectURL'],
                ],
            ]);
        }

        $connectInfo = AccountConnect::where('account_id', $cacheData['accountId'])->where('connect_platform_id', $cacheData['connectPlatformId'])->first();

        $fresnsLang = ConfigHelper::fresnsConfigLanguagePack($langTag);

        $btnName = $fresnsLang['settingAccountDisconnect'];

        return view('GitHubSignIn::connect.disconnect', compact('authUlid', 'connectInfo', 'btnName'));
    }

    // github-signin.connect.disconnect.result
    public function disconnectResult(Request $request)
    {
        $authUlid = $request->authUlid;
        $langTag = $request->langTag ?? AppHelper::getLangTag();

        // Verify usage permissions.
        $cacheData = CacheHelper::get($authUlid, 'fresnsPluginAuth');
        if (empty($cacheData)) {
            return view('GitHubSignIn::tips', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
                'data' => [
                    'redirectURL' => null,
                ],
            ]);
        }

        // Check sign in
        if (empty($cacheData['accountId'])) {
            return view('GitHubSignIn::tips', [
                'code' => 31501,
                'message' => ConfigUtility::getCodeMessage(31501, 'Fresns', $langTag),
                'data' => [
                    'redirectURL' => $cacheData['redirectURL'],
                ],
            ]);
        }

        $wordBody = [
            'aid' => $cacheData['headers']['aid'],
            'connectPlatformId' => $cacheData['connectPlatformId'],
        ];

        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->disconnectAccountConnect($wordBody);

        return view('GitHubSignIn::tips', [
            'code' => $fresnsResp->getCode(),
            'message' => $fresnsResp->isSuccessResponse() ? ConfigUtility::getCodeMessage(0, 'Fresns', $langTag) : $fresnsResp->getMessage(),
            'data' => [
                'redirectURL' => $cacheData['redirectURL'],
            ],
        ]);
    }

    // Auth Callback
    public function authCallback(Request $request)
    {
        $authUlid = $request->authUlid;
        $langTag = $request->langTag ?? AppHelper::getLangTag();

        // Verify usage permissions.
        $cacheData = CacheHelper::get($authUlid, 'fresnsPluginAuth');
        if (empty($cacheData)) {
            return view('GitHubSignIn::tips', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
                'data' => [
                    'redirectURL' => null,
                ],
            ]);
        }

        $redirectURL = $cacheData['redirectURL'];

        // If your plugin supports multiple platforms, you can differentiate handling based on the platform ID.
        $connectPlatformId = $cacheData['connectPlatformId'];

        $fresnsLang = ConfigHelper::fresnsConfigLanguagePack($langTag);

        try {
            SignHelper::setDriverConfig();
            $githubUser = Socialite::driver('github')->user();

            $accountConnectDetail = [
                'fskey' => 'GitHubSignIn', // setAccountConnect
                'aid' => $cacheData['headers']['aid'] ?? null, // setAccountConnect
                'connectPlatformId' => $connectPlatformId,
                'connectAccountId' => $githubUser->id,
                'connectToken' => $githubUser->token,
                'connectRefreshToken' => $githubUser->refreshToken,
                'refreshTokenExpiredDatetime' => $githubUser->expiresIn,
                'connectUsername' => $githubUser->nickname, // github username
                'connectNickname' => $githubUser->name, // github nickname
                'connectAvatar' => $githubUser->avatar,
                'connectEmail' => $githubUser->email,
                'appFskey' => 'GitHubSignIn', // createAccount
                'moreInfo' => [], // createAccount
            ];
        } catch (\Exception $e) {
            return redirect()->intended($redirectURL)->with('failure', $fresnsLang['errorUnavailable']);
        }

        // Request when logged in.
        if ($cacheData['accountId']) {
            $fresnsResp = \FresnsCmdWord::plugin('Fresns')->setAccountConnect($accountConnectDetail);

            if ($fresnsResp->isErrorResponse()) {
                return redirect()->intended($redirectURL)->with('failure', $fresnsResp->getMessage());
            }

            return redirect()->intended($redirectURL)->with('success', $fresnsLang['success']);
        }

        // Request when not logged in.
        $wordBody = [
            'type' => Account::VERIFY_TYPE_CONNECT,
            'connectPlatformId' => $connectPlatformId,
            'connectAccountId' => $githubUser->id,
        ];

        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->verifyAccount($wordBody);

        if (in_array($fresnsResp->getCode(), [31502, 34301])) {
            $githubUlid = (string) Str::ulid();

            CacheHelper::put($accountConnectDetail, $githubUlid, 'fresnsPluginAuth', 10);

            $createAccountUrl = route('github-signin.create.account', [
                'authUlid' => $authUlid,
                'githubUlid' => $githubUlid,
                'langTag' => $langTag,
            ]);

            $siteLogo = ConfigHelper::fresnsConfigFileUrlByItemKey('site_logo');

            return view('GitHubSignIn::connect.check-sign', compact('siteLogo', 'fresnsLang', 'accountConnectDetail', 'createAccountUrl'));
        }

        if ($fresnsResp->isErrorResponse()) {
            return redirect()->intended($redirectURL)->with('failure', $fresnsResp->getMessage());
        }

        $aid = $fresnsResp->getData('aid');

        $loginToken = SignHelper::getLoginToken(SessionLog::TYPE_ACCOUNT_LOGIN, $cacheData['headers'], $aid);

        // Replace the variable value of loginToken in the URL.
        $redirectURL = Str::replace('{loginToken}', $loginToken, $redirectURL);

        return redirect()->intended($redirectURL);
    }

    public function createAccount(Request $request)
    {
        $authUlid = $request->authUlid;
        $githubUlid = $request->githubUlid;
        $langTag = $request->langTag;

        // Verify usage permissions.
        $cacheData = CacheHelper::get($authUlid, 'fresnsPluginAuth');
        if (empty($cacheData)) {
            return view('GitHubSignIn::tips', [
                'code' => 32203,
                'message' => ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
                'data' => [
                    'redirectURL' => null,
                ],
            ]);
        }

        // Get github data
        $githubData = CacheHelper::get($githubUlid, 'fresnsPluginAuth');
        if (empty($githubData)) {
            return view('GitHubSignIn::tips', [
                'code' => 32203,
                'message' => '[GitHub Data] '.ConfigUtility::getCodeMessage(32203, 'Fresns', $langTag),
                'data' => [
                    'redirectURL' => $cacheData['redirectURL'],
                ],
            ]);
        }

        if (empty($githubData['connectAccountId'] ?? null)) {
            return view('GitHubSignIn::tips', [
                'code' => 30001,
                'message' => '[GitHub ID] '.ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
                'data' => [
                    'redirectURL' => $cacheData['redirectURL'],
                ],
            ]);
        }

        // create account
        $wordBody = [
            'type' => Account::CREATE_TYPE_CONNECT,
            'account' => '',
            'connectInfo' => [
                $githubData,
            ],
            'createUser' => true,
            'userInfo' => [
                'nickname' => $githubData['connectNickname'],
                'avatarUrl' => $githubData['connectAvatar'],
            ],
        ];

        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->createAccount($wordBody);

        $headers = $cacheData['headers'];

        // session log
        $sessionLog = [
            'type' => SessionLog::TYPE_ACCOUNT_REGISTER,
            'appId' => $headers['appId'],
            'platformId' => $headers['platformId'],
            'version' => $headers['version'],
            'langTag' => $headers['langTag'],
            'fskey' => 'GitHubSignIn',
            'actionName' => request()->path(),
            'actionDesc' => 'Create Account',
            'actionState' => SessionLog::STATE_SUCCESS,
            'actionId' => null,
            'aid' => null,
            'uid' => null,
            'deviceInfo' => AppHelper::getDeviceInfo(),
            'deviceToken' => null,
            'loginToken' => null,
            'moreInfo' => null,
        ];

        if ($fresnsResp->isErrorResponse()) {
            // create session log
            $sessionLog['actionState'] = SessionLog::STATE_FAILURE;

            \FresnsCmdWord::plugin('Fresns')->createSessionLog($sessionLog);

            return view('GitHubSignIn::tips', [
                'code' => $fresnsResp->getCode(),
                'message' => $fresnsResp->getMessage(),
                'data' => [
                    'redirectURL' => $cacheData['redirectURL'],
                ],
            ]);
        }

        $loginToken = AppSignHelper::makeLoginToken($fresnsResp->getData('aid'));

        $sessionLog['aid'] = $fresnsResp->getData('aid');
        $sessionLog['uid'] = $fresnsResp->getData('uid');
        $sessionLog['loginToken'] = $loginToken;
        \FresnsCmdWord::plugin('Fresns')->createSessionLog($sessionLog);

        // Replace the variable value of loginToken in the URL.
        $redirectURL = Str::replace('{loginToken}', $loginToken, $cacheData['redirectURL']);

        return redirect()->intended($redirectURL);
    }
}
