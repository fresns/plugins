<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Http\Controllers;

use App\Helpers\ConfigHelper;
use App\Helpers\FileHelper;
use App\Helpers\PrimaryHelper;
use App\Utilities\ConfigUtility;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class QiNiuController extends Controller
{
    public function upload(Request $request)
    {
        $headers = json_decode(base64_decode(urldecode($request->sign)), true);
        $uploadInfo = json_decode(base64_decode(urldecode($request->config)), true);

        $langTag = $headers['langTag'] ?? ConfigHelper::fresnsConfigDefaultLangTag();
        \View::share('langTag', $langTag);

        // 判断必传参数
        if (! $request->sign || ! $request->config) {
            return view('QiNiu::error', [
                'code' => 30001,
                'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        // 验证签名
        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->verifyUrlSign([
            'urlSign' => $request->sign,
        ]);

        if ($fresnsResp->isErrorResponse()) {
            return view('QiNiu::error', [
                'code' => 31503,
                'message' => ConfigUtility::getCodeMessage(31503, 'Fresns', $langTag),
            ]);
        }

        // 验证是否登录状态
        if (! $fresnsResp->getData('uid')) {
            return view('QiNiu::error', [
                'code' => 31601,
                'message' => ConfigUtility::getCodeMessage(31601, 'Fresns', $langTag),
            ]);
        }

        // 上传文件必传参数 https://fresns.cn/api/common/upload-file.html
        if (! $uploadInfo['usageType'] || ! $uploadInfo['tableName'] || ! $uploadInfo['type']) {
            return view('QiNiu::error', [
                'code' => 30002,
                'message' => ConfigUtility::getCodeMessage(30002, 'Fresns', $langTag),
            ]);
        }

        if (! $uploadInfo['tableId'] && ! $uploadInfo['tableKey']) {
            return view('QiNiu::error', [
                'code' => 30002,
                'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        // 获取文件配置
        $fileType = match ($uploadInfo['type']) {
            'image' => 1,
            'video' => 2,
            'audio' => 3,
            'document' => 4,
        };

        $usageType = match ($uploadInfo['usageType']) {
            7 => 'post',
            8 => 'comment',
        };

        $authUserId = PrimaryHelper::fresnsUserIdByUidOrUsername($headers['uid']);

        $editorConfig = ConfigUtility::getEditorConfigByType($authUserId, $usageType, $langTag);
        $toolbar = $editorConfig['toolbar'][$uploadInfo['type']];

        $uploadConfig = [
            'status' => $toolbar['status'],
            'extensions' => $toolbar['extensions'],
            'inputAccept' => $toolbar['inputAccept'],
            'maxSize' => $toolbar['maxSize'],
            'maxTime' => $toolbar['maxTime'] ?? 0,
            'uploadNumber' => $toolbar['uploadNumber'],
        ];

        $fsLang = ConfigHelper::fresnsConfigByItemKey('language_pack_contents', $langTag);

        $postMessageKey = $request->postMessageKey ?? null;

        $dir = FileHelper::fresnsFileStoragePath($fileType, $uploadInfo['usageType']);
        $checkHeaders = $fresnsResp->getData();

        $uploadTokenResp = \FresnsCmdWord::plugin('QiNiu')->getUploadToken([
            'type' => $fileType,
            'name' => null,
            'expireTime' => 3600,
        ]);

        $uploadToken = $uploadTokenResp->getData('token');

        return view('QiNiu::index', compact(
            'langTag',
            'uploadInfo',
            'fileType',
            'checkHeaders',
            'dir',
            'uploadToken',
            'fsLang',
            'uploadConfig',
            'postMessageKey'
        ));
    }
}