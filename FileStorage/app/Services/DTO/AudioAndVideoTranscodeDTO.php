<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FileStorage\Services\DTO;

use Fresns\DTO\DTO;

class AudioAndVideoTranscodeDTO extends DTO
{
    public function rules(): array
    {
        return [
            'tableName' => ['string', 'required'],
            'primaryId' => ['integer', 'required'],
            'changeType' => ['string', 'required'],
        ];
    }
}
