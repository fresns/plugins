<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\Cloudinary\Http\Controllers;

use App\Helpers\ConfigHelper;
use App\Helpers\FileHelper;
use App\Helpers\PrimaryHelper;
use App\Models\File;
use App\Models\FileUsage;
use App\Utilities\ConfigUtility;
use Cloudinary\Api\ApiUtils;
use Cloudinary\Configuration\CloudConfig;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Plugins\Cloudinary\Http\Requests\UploadFileInfoDTO;

class WebController extends Controller
{
    // upload
    public function upload(Request $request)
    {
        // Verify URL Authorization
        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->verifyUrlAuthorization([
            'urlAuthorization' => $request->authorization,
            'userLogin' => true,
        ]);

        $langTag = $fresnsResp->getData('langTag');
        View::share('langTag', $langTag);

        if ($fresnsResp->isErrorResponse()) {
            return view('Cloudinary::error', [
                'code' => $fresnsResp->getCode(),
                'message' => $fresnsResp->getMessage(),
            ]);
        }

        // Judgment required parameter
        if (empty($request->config)) {
            return view('Cloudinary::error', [
                'code' => 30001,
                'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        $uploadInfo = json_decode(base64_decode(urldecode($request->config)), true);

        // Required parameter https://fresns.org/api/common/upload-file.html
        if (! $uploadInfo['usageType'] || ! $uploadInfo['tableName'] || ! $uploadInfo['type']) {
            return view('Cloudinary::error', [
                'code' => 30002,
                'message' => ConfigUtility::getCodeMessage(30002, 'Fresns', $langTag),
            ]);
        }

        if (! $uploadInfo['tableId'] && ! $uploadInfo['tableKey']) {
            return view('Cloudinary::error', [
                'code' => 30002,
                'message' => ConfigUtility::getCodeMessage(30001, 'Fresns', $langTag),
            ]);
        }

        // Get file config
        $fileType = match ($uploadInfo['type']) {
            'image' => File::TYPE_IMAGE,
            'video' => File::TYPE_VIDEO,
            'audio' => File::TYPE_AUDIO,
            'document' => File::TYPE_DOCUMENT,
        };

        $usageType = match ($uploadInfo['usageType']) {
            FileUsage::TYPE_POST => 'post',
            FileUsage::TYPE_COMMENT => 'comment',
        };

        $authUserId = PrimaryHelper::fresnsUserIdByUidOrUsername($fresnsResp->getData('uid'));

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
        $headers = $fresnsResp->getData();

        // Number of files uploaded
        $fileCount = FileUsage::where('file_type', $fileType)
            ->where('usage_type', $uploadInfo['usageType'])
            ->where('table_name', $uploadInfo['tableName'])
            ->where('table_column', $uploadInfo['tableColumn'])
            ->where('table_id', $uploadInfo['tableId'])
            ->count();

        $fileCountTip = ConfigUtility::getCodeMessage(36115, 'Fresns', $langTag);

        $fileMax = $uploadConfig['uploadNumber'] - $fileCount;

        // Get upload token
        $uploadTokenResp = \FresnsCmdWord::plugin('Cloudinary')->getUploadToken([
            'type' => $fileType,
            'name' => null,
            'expireTime' => 3600,
        ]);

        $uploadToken = $uploadTokenResp->getData('token');

        $fsError = [
            'fileType' => ConfigUtility::getCodeMessage(36310, 'Fresns', $langTag),
            'fileSize' => ConfigUtility::getCodeMessage(36113, 'Fresns', $langTag),
            'fileTime' => ConfigUtility::getCodeMessage(36114, 'Fresns', $langTag),
        ];

        return view('Cloudinary::upload', compact(
            'langTag',
            'uploadInfo',
            'fileType',
            'headers',
            'dir',
            'uploadToken',
            'fsLang',
            'fsError',
            'uploadConfig',
            'fileCount',
            'fileCountTip',
            'fileMax',
            'postMessageKey',
        ));
    }

    // getSignData
    public function getSignData()
    {
        request()->validate([
            'type' => ['required', 'integer'],
            'usageType' => ['required', 'integer'],
        ]);

        // $fileType = match (request('type')) {
        //     'image' => File::TYPE_IMAGE,
        //     'video' => File::TYPE_VIDEO,
        //     'audio' => File::TYPE_AUDIO,
        //     'document' => File::TYPE_DOCUMENT,
        // };
        $fileType = request()->integer('type');
        $usageType = request()->integer('usageType');

        $config = FileHelper::fresnsFileStorageConfigByType($fileType); // file config
        $cloudname = $config['bucketName'];

        $cloudConfig = new CloudConfig();
        $cloudConfig->setCloudConfig('cloud_name', $cloudname);
        $cloudConfig->setCloudConfig('api_key', $config['secretId']);
        $cloudConfig->setCloudConfig('api_secret', $config['secretKey']);

        $dir = FileHelper::fresnsFileStoragePath($fileType, $usageType);

        $paramsToSign = [
            'timestamp' => time(),
            'eager' => '',
            'folder' => $dir,
        ];
        ApiUtils::signRequest($paramsToSign, $cloudConfig);

        return response()->json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'cloudname' => $cloudname,
                'apikey' => $paramsToSign['api_key'],
                'timestamp' => $paramsToSign['timestamp'],
                'signature' => $paramsToSign['signature'],
                'eager' => $paramsToSign['eager'],
                'folder' => $paramsToSign['folder'],
            ],
        ]);
    }

    // uploadFileInfo
    public function uploadFileInfo(Request $request)
    {
        $dtoRequest = new UploadFileInfoDTO($request->all());

        $bodyInfo = [
            'platformId' => $dtoRequest->platformId,
            'usageType' => $dtoRequest->usageType,
            'tableName' => $dtoRequest->tableName,
            'tableColumn' => $dtoRequest->tableColumn,
            'tableId' => $dtoRequest->tableId ?? null,
            'tableKey' => $dtoRequest->tableKey ?? null,
            'aid' => $dtoRequest->aid,
            'uid' => $dtoRequest->uid,
            'type' => (int) $dtoRequest->type,
            'fileInfo' => $dtoRequest->fileInfo,
        ];

        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->uploadFileInfo($bodyInfo);

        if ($fresnsResp->isErrorResponse()) {
            return $fresnsResp->errorResponse();
        }

        return response()->json([
            'code' => 0,
            'message' => 'success',
            'data' => $fresnsResp->getData(),
        ]);
    }
}
