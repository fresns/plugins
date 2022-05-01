<?php

namespace Plugins\QiNiu\Events;

use Plugins\QiNiu\QiNiu;
use Illuminate\Queue\SerializesModels;

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