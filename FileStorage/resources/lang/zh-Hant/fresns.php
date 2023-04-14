<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Fresns Email Language Lines
    |--------------------------------------------------------------------------
    */

    'name' => 'Fresns 文件存儲',
    'description' => 'Fresns 官方開發的「文件存儲」服務外掛，支持本地、FTP、SFTP 三種存儲方式。',

    'test' => '測試',

    'driver' => '磁盤',
    'driverIntro' => '支持 local, ftp, sftp',

    'privateKeyIntro' => 'SFTP 專用，如果使用 SSH 密鑰驗證方式必填，否則留空',
    'passphraseIntro' => 'SFTP 專用，如果密鑰無密碼則留空',
    'hostFingerprintIntro' => 'SFTP 專用，沒有要求則留空',

    'imageProcessingStatus' => '圖片處理功能',
    'imageProcessingLibrary' => '圖片處理庫',
    'imageProcessingLibraryIntro' => '支持 GD 和 Imagick',

    'imageMaxWidth' => '寬度上限，單位：像素 px',
    'imageSquareSize' => '正方形尺寸，單位：像素 px',

    'imageWatermarkFile' => '水印圖片',
    'imageWatermarkPosition' => '水印位置',
    'imageWatermarkConfig' => '水印配置',
];
