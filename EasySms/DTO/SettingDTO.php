<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasySms\DTO;

use Fresns\DTO\DTO;

class SettingDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'easysms_type' => ['required', 'integer'],
            'easysms_keyid' => ['required', 'string'],
            'easysms_keysecret' => ['required', 'string'],
            'easysms_sdk_appid' => ['nullable', 'string'],
            'easysms_linked' => ['required', 'string'],
        ];
    }
}
