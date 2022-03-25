<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEmail\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Config extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\ConfigTrait;

    protected $guarded = [];

    public function languages()
    {
        return $this->hasMany(Language::class, 'table_key', 'item_key')
            ->where('table_name', 'configs');
    }
}
