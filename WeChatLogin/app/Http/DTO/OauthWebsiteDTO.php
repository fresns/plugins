<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\WeChatLogin\Http\DTO;

use Fresns\DTO\DTO;

class OauthWebsiteDTO extends DTO
{
    public function rules(): array
    {
        return [
            'code' => ['string', 'required'],
            'ulid' => ['ulid', 'required'],
            'autoRegister' => ['boolean', 'nullable'],
            'nickname' => ['string', 'nullable'],
            'avatarUrl' => ['string', 'nullable'],
        ];
    }
}
