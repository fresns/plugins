<?php

namespace Plugins\QiNiu\Services;

use Fresns\DTO\DTO;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;

class UploadToken extends DTO
{
    use QiNiuStorageTrait;

    public function rules(): array
    {
        return [
            'type' => ['integer', 'required'],
            'name' => ['string', 'required'],
            'expireTime' => ['integer', 'required'],
        ];
    }

    public function getToken()
    {
        return $this->getAdapter()?->getUploadToken($this->name, $this->getExpireTime());
    }

    public function getExpireTime()
    {
        return $this->expireTime ?? $this->getDefaultExpireTime();
    }
}