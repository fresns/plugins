<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FileStorage\Services\DTO;

use Fresns\DTO\DTO;

/**
 * Class AudioAndVideoTranscodeDTO.
 */
class AudioAndVideoTranscodeDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'tableName' => ['string', 'required'],
            'primaryId' => ['integer', 'required'],
            'changeType' => ['string', 'required'],
        ];
    }
}
