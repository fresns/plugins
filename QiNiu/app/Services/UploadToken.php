<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

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
            'name' => ['string', 'nullable'],
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
