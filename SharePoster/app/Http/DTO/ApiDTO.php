<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\SharePoster\Http\DTO;

use Fresns\DTO\DTO;

class ApiDTO extends DTO
{
    public function rules(): array
    {
        return [
            'type' => ['string', 'required', 'in:user,group,hashtag,post,comment'],
            'fsid' => ['string', 'required'],
            'langTag' => ['string', 'nullable'],
        ];
    }
}
