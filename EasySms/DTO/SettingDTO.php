<?php

namespace Plugins\EasySms\DTO;

use Fresns\DTO\DTO;

/**
 * @property-read string sence
 * @property-read string countryCode
 * @property-read string phoneNumber
 * @property-read string signName
 * @property-read string templateCode
 * @property-read string templateParam
 */
class SettingDTO extends DTO
{
    /**
    * @return array
    */
    public function rules(): array
    {
        return [
            'easysms_type' => 'required|string',
            'easysms_keyid' => 'required',
            'easysms_keysecret' => 'required',
            'easysms_appid' => 'nullable',
            'easysms_linked' => 'required|string',
        ];
    }
}
