<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\FileUsage;
use App\Utilities\ConfigUtility;
use App\Utilities\SubscribeUtility;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected $fresnsConfigItems = [
        // image
        [
            'item_key' => 'filestorage_image_driver',
            'item_value' => 'local', // local, ftp, sftp
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_image_private_key',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_image_passphrase',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_image_host_fingerprint',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_image_processing_status',
            'item_value' => 'open',
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_image_processing_library',
            'item_value' => 'gd',
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_image_processing_params',
            'item_value' => '{"config":400,"ratio":400,"square":200,"big":1500}',
            'item_type' => 'object',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_image_watermark_file',
            'item_value' => null,
            'item_type' => 'file',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_image_watermark_position',
            'item_value' => 'top-left',
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],

        // video
        [
            'item_key' => 'filestorage_video_driver',
            'item_value' => 'local', // local, ftp, sftp
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_video_private_key',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_video_passphrase',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_video_host_fingerprint',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],

        // audio
        [
            'item_key' => 'filestorage_audio_driver',
            'item_value' => 'local', // local, ftp, sftp
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_audio_private_key',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_audio_passphrase',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_audio_host_fingerprint',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],

        // document
        [
            'item_key' => 'filestorage_document_driver',
            'item_value' => 'local', // local, ftp, sftp
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_document_private_key',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_document_passphrase',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
        [
            'item_key' => 'filestorage_document_host_fingerprint',
            'item_value' => null,
            'item_type' => 'string',
            'item_tag' => 'filestorage',
        ],
    ];

    protected $fresnsWordBody = [
        'type' => SubscribeUtility::TYPE_TABLE_DATA_CHANGE,
        'fskey' => 'FileStorage',
        'cmdWord' => 'audioAndVideoTranscode',
        'subTableName' => FileUsage::class,
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        ConfigUtility::addFresnsConfigItems($this->fresnsConfigItems);

        \FresnsCmdWord::plugin()->addSubscribeItem($this->fresnsWordBody);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        ConfigUtility::removeFresnsConfigItems($this->fresnsConfigItems);

        \FresnsCmdWord::plugin()->removeSubscribeItem($this->fresnsWordBody);
    }
};
