<?php

namespace Plugins\EasySms\DTO;

use App\Fresns\Words\Basis\DTO\SendCodeDTO;

/**
 * @property-read string sence
 * @property-read string countryCode
 * @property-read string phoneNumber
 * @property-read string signName
 * @property-read string templateCode
 * @property-read string templateParam
 */
class SmsSendCodeDTO extends SendCodeDTO
{
    /**
    * @return array
    */
    public function rules(): array
    {
        return [
            'type' => 'required|integer',
            'account' => 'required|string',
            'countryCode' => 'nullable|integer',
            'templateId' => 'required|integer',
            'langTag' => 'required|string',
        ];
    }

    public function setTypeAttribute(int $type)
    {
        if ($type === 1) {
            throw new \LogicException('服务商不支持邮件发信');
        }

        return $type;
    }
}
