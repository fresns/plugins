<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasySms\Models\Traits;

trait ConfigTrait
{
    public function getItemValueAttribute($value)
    {
        if (in_array($this->item_type, ['array', 'plugins', 'object'])) {
            $value = json_decode($value, true) ?: [];
        } elseif ($this->item_type == 'boolean') {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return $value;
    }

    public function setItemValueAttribute($value)
    {
        if (in_array($this->item_type, ['array', 'plugins', 'object']) || is_array($value)) {
            $value = json_encode($value);
        }

        if ($this->item_type == 'boolean') {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
        }

        $this->attributes['item_value'] = $value;
    }

    public function scopeTag($query, $value)
    {
        return $query->where('item_tag', $value);
    }
}
