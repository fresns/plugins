<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Events;

use Illuminate\Queue\SerializesModels;
use Plugins\QiNiu\QiNiu;

class UploadTokenGenerated
{
    use SerializesModels;

    public QiNiu $disk;

    public string $token;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(QiNiu $disk, $token)
    {
        $this->disk = $disk;
        $this->token = $token;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
