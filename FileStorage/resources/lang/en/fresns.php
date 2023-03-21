<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Fresns Email Language Lines
    |--------------------------------------------------------------------------
    */

    'name' => 'Fresns File Storage',
    'description' => 'The official File Storage service plugin developed by Fresns. Supports local, ftp and sftp storage methods.',

    'test' => 'Test',

    'driver' => 'Driver',
    'driverIntro' => 'Supports local, ftp, sftp',

    'privateKeyIntro' => 'SFTP specific, required if using SSH key authentication, otherwise leave empty',
    'passphraseIntro' => 'SFTP specific, leave empty if key has no password',
    'hostFingerprintIntro' => 'SFTP specific, leave empty if not required',

    'imageProcessingLibrary' => 'Image Processing Library',
    'imageProcessingLibraryIntro' => 'Support for GD & Imagick',

    'imageMaxWidth' => 'Maximum width in pixels px',
    'imageSquareSize' => 'Square size in pixels px',

    'imageWatermarkFile' => 'Watermark Image',
    'imageWatermarkConfig' => 'Watermark Config',
];
