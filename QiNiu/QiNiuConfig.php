<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu;

class QiNiuConfig
{
    const TYPE_IMAGE = 1;   // 图片
    const TYPE_VIDEO = 2;   // 视频
    const TYPE_AUDIO = 3;   // 音频
    const TYPE_DOCUMENT = 4;     // 文档

    // 防盗链 图片
    const IMAGE_URL_STATUS = 'image_url_status';   // 防盗链开启状态
    const IMAGE_URL_KEY = 'image_url_key';         // 防盗链 key
    const IMAGE_URL_EXPIRE = 'image_url_expire';   // 防盗链过期时间，单位：分钟

    // 防盗链 视频
    const VIDEO_URL_STATUS = 'video_url_status';   // 防盗链开启状态
    const VIDEO_URL_KEY = 'video_url_key';         // 防盗链 key
    const VIDEO_URL_EXPIRE = 'video_url_expire';   // 防盗链过期时间，单位：分钟

    // 防盗链 音频
    const AUDIO_URL_STATUS = 'audio_url_status';   // 防盗链开启状态
    const AUDIO_URL_KEY = 'audio_url_key';         // 防盗链 key
    const AUDIO_URL_EXPIRE = 'audio_url_expire';   // 防盗链过期时间，单位：分钟

    // 防盗链 音频
    const DOCUMENT_URL_STATUS = 'document_url_status';       // 防盗链开启状态
    const DOCUMENT_URL_KEY = 'document_url_key';             // 防盗链 key
    const DOCUMENT_URL_EXPIRE = 'document_url_expire';       // 防盗链过期时间，单位：分钟
}
