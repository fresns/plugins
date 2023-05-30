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
        $userId = PrimaryHelper::fresnsUserIdByUidOrUsername($uid);

        if (empty($uid) || empty($userId)) {
            return $this->failure(21008);
        }

        $cacheKey = "fresns_online_days_{$userId}";
        $cacheTags = ['fresnsPlugins', 'pluginOnlineDays'];

        $userCache = CacheHelper::get($cacheKey, $cacheTags);
        if (empty($userCache)) {
            // Get the current date and time
            $currentDateTime = new \DateTime();

            // Get the end of day date time
            $endOfDay = clone $currentDateTime;
            $endOfDay->setTime(23, 59, 59);

            // Calculating the remaining time interval
            $remainingTime = $currentDateTime->diff($endOfDay);

            // Get the number of hours and minutes of time left
            $remainingHours = $remainingTime->h;
            $remainingMinutes = $remainingTime->i;

            // Use the remaining time to set the validity of the cache
            $cacheExpiration = now()->addHours($remainingHours)->addMinutes($remainingMinutes);

            // Have you recorded today
            $todayLog = UserExtcreditsLog::where('user_id', $userId)->where('extcredits_id', $extcreditsId)->whereDate('created_at', date('Y-m-d'))->first();

            if ($todayLog) {
                CacheHelper::put(now(), $cacheKey, $cacheTags, 10, $cacheExpiration);

                return $this->success();
            }

            $extWordBody = [
                'uid' => $uid,
                'extcreditsId' => $extcreditsId,
                'fskey' => 'OnlineDays',
                'operation' => 'increment',
            ];
            \FresnsCmdWord::plugin('Fresns')->setUserExtcredits($extWordBody);

            CacheHelper::put(now(), $cacheKey, $cacheTags, 10, $cacheExpiration);
        }

        return $this->success();
    }
}
