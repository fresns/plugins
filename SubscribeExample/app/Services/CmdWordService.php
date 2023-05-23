<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\SubscribeExample\Services;

use Fresns\CmdWordManager\Traits\CmdWordResponseTrait;

class CmdWordService
{
    use CmdWordResponseTrait;

    // DataChange
    public function dataChange($wordBody)
    {
        \info('Subscribe Data Table Change: ', [$wordBody]);

        return $this->success();
    }

    // UserActivity
    public function userActivity($wordBody)
    {
        \info('Subscribe User Activity: ', [$wordBody]);

        return $this->success();
    }

    // AccountAndUserLogin
    public function accountAndUserLogin($wordBody)
    {
        \info('Subscribe Account and User Login: ', [$wordBody['primaryId']]);

        return $this->success();
    }

    // viewContent
    public function viewContent($wordBody)
    {
        \info('Subscribe View Content: ', [$wordBody]);

        return $this->success();
    }
}
