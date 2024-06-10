<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\OnlineDays\Services;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\PrimaryHelper;
use App\Models\UserExtcreditsLog;
use Fresns\CmdWordManager\Traits\CmdWordResponseTrait;

class CmdWordService
{
    use CmdWordResponseTrait;

    // stats
    public function stats($wordBody)
    {
        $extcreditsId = ConfigHelper::fresnsConfigByItemKey('online_days_extcredits_id');
        if (empty($extcreditsId)) {
            return $this->failure(21010);
        }

        $uid = $wordBody['headers']['x-fresns-uid'] ?? null;
        $userId = PrimaryHelper::fresnsPrimaryId('user', $uid);

        if (empty($uid) || empty($userId)) {
            return $this->failure(21008);
        }

        $cacheKey = "fresns_online_days_{$userId}";
        $cacheTags = ['fresnsPlugins', 'pluginOnlineDays'];

        $userCache = CacheHelper::get($cacheKey, $cacheTags);

        if ($userCache) {
            return $this->success();
        }

        // Record in the cache that the operation is done
        $cacheExpiration = now()->endOfDay();  // Set cache to expire at the end of the day
        CacheHelper::put(now(), $cacheKey, $cacheTags, $cacheExpiration, 10);

        // Check if there's already a log entry for today
        $todayLogExists = UserExtcreditsLog::where('user_id', $userId)->where('extcredits_id', $extcreditsId)->whereDate('created_at', date('Y-m-d'))->exists();

        if ($todayLogExists) {
            return $this->success();
        }

        // If no log entry, proceed to log the credits
        $extWordBody = [
            'uid' => $uid,
            'extcreditsId' => $extcreditsId,
            'fskey' => 'OnlineDays',
            'operation' => 'increment',
        ];
        \FresnsCmdWord::plugin('Fresns')->setUserExtcredits($extWordBody);

        return $this->success();
    }
}
