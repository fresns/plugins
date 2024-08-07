<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\GitHubSignIn\Helpers;

use App\Helpers\AppHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\SignHelper as AppSignHelper;
use App\Models\SessionLog;

class SignHelper
{
    public static function setDriverConfig(?string $callbackUrl = null): void
    {
        $configs = ConfigHelper::fresnsConfigByItemKeys([
            'github_signin_client_id',
            'github_signin_client_secret',
        ]);

        config([
            'services.github.client_id' => $configs['github_signin_client_id'],
            'services.github.client_secret' => $configs['github_signin_client_secret'],
            'services.github.redirect' => $callbackUrl,
        ]);
    }

    public static function getLoginToken(int $type, array $headers, string $aid): string
    {
        // loginToken
        $loginToken = AppSignHelper::makeLoginToken($aid);

        // session log
        $sessionLog = [
            'type' => $type,
            'appId' => $headers['appId'],
            'platformId' => $headers['platformId'],
            'version' => $headers['version'],
            'langTag' => $headers['langTag'],
            'fskey' => 'GitHubSignIn',
            'actionName' => request()->path(),
            'actionDesc' => 'Account Login',
            'actionState' => SessionLog::STATE_SUCCESS,
            'actionId' => null,
            'aid' => $aid,
            'uid' => null,
            'deviceInfo' => AppHelper::getDeviceInfo(),
            'deviceToken' => null,
            'loginToken' => $loginToken,
            'moreInfo' => null,
        ];

        // create session log
        \FresnsCmdWord::plugin('Fresns')->createSessionLog($sessionLog);

        return $loginToken;
    }
}
