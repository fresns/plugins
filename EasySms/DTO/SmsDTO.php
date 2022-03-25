<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasySms\DTO;

use App\Fresns\Words\Send\DTO\SendSmsDTO;

/**
 * @property-read string sence
 * @property-read string countryCode
 * @property-read string phoneNumber
 * @property-read string signName
 * @property-read string templateCode
 * @property-read string templateParam
 */
class SmsDTO extends SendSmsDTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'countryCode' => 'required',
            'phoneNumber' => 'required',
            'signName' => 'nullable|string',
            'content' => 'nullable|string',
            'templateCode' => 'required',
            'templateParam' => 'nullable|string',
        ];
    }

    public function getTemplateParamAttribute(): array
    {
        return json_decode($this->getItem('templateParam'), true) ?? [];
    }
}
