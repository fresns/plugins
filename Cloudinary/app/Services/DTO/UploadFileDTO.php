<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\Cloudinary\Services\DTO;

use Fresns\DTO\DTO;

class UploadFileDTO extends DTO
{
    public function rules(): array
    {
        return [
            'platformId' => ['integer', 'required', 'between:1,13'],
            'usageType' => ['integer', 'required', 'between:1,10'],
            'tableName' => ['string', 'required'],
            'tableColumn' => ['string', 'required'],
            'tableId' => ['integer', 'nullable', 'required_without:tableKey'],
            'tableKey' => ['string', 'nullable', 'required_without:tableId'],
            'aid' => ['string', 'nullable', 'exists:App\Models\Account,aid'],
            'uid' => ['integer', 'nullable', 'exists:App\Models\User,uid'],
            'type' => ['integer', 'required', 'in:1,2,3,4'],
            'file' =>['file', 'required'],
            'moreJson' => ['array', 'nullable'],
        ];
    }
}
