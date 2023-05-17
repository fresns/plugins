<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\WeChatLogin\Http\DTO;

use Fresns\DTO\DTO;

class OauthDTO extends DTO
{
    public function rules(): array
    {
        return [
            'code' => ['string', 'required'],
            'autoRegister' => ['boolean', 'nullable'],
        ];
    }
}
