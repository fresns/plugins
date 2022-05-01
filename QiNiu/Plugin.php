<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu;

use App\Fresns\Api\Center\Base\BasePlugin;
use App\Fresns\Api\Center\Common\ErrorCodeService;
use App\Fresns\Api\Center\Helper\CmdRpcHelper;
use App\Fresns\Api\Center\Scene\FileSceneService;
use App\Fresns\Api\FsCmd\FresnsCmdWords;
use App\Fresns\Api\FsCmd\FresnsCmdWordsConfig;
use App\Fresns\Api\FsDb\FresnsComments\FresnsComments;
use App\Fresns\Api\FsDb\FresnsFileAppends\FresnsFileAppends;
use App\Fresns\Api\FsDb\FresnsFiles\FresnsFiles;
use App\Fresns\Api\FsDb\FresnsPluginCallbacks\FresnsPluginCallbacks;
use App\Fresns\Api\FsDb\FresnsPosts\FresnsPosts;
use App\Helpers\ConfigHelper;
use Plugins\QiNiu\ServicesOld\QiNiuAudioService;
use Plugins\QiNiu\ServicesOld\QiNiuDocService;
use Plugins\QiNiu\ServicesOld\QiNiuImageService;
use Plugins\QiNiu\ServicesOld\QiNiuService;
use Plugins\QiNiu\ServicesOld\QiNiuTransService;
use Plugins\QiNiu\ServicesOld\QiNiuVideoService;

class Plugin extends BasePlugin
{
    public function __construct()
    {
        $this->pluginConfig = new PluginConfig();
        $this->pluginCmdHandlerMap = PluginConfig::FRESNS_CMD_HANDLE_MAP;
    }

    public function getCodeMap()
    {
        return PluginConfig::CODE_MAP;
    }

    // 获取上传凭证
    public function plgCmdGetUploadTokenHandler($input)
    {
        $type = $input['type'];
        $scene = $input['scene'];

        $qiNiuService = new QiNiuService($type);
        $key = $qiNiuService->generatQiNiuKey($type);
        $token = $qiNiuService->getUploadToken($type, $key);

        $output = $input;
        $output['token'] = $token;
        $output['storageId'] = 17;
        $output['expireTime'] = null;

        // 生成后，不仅输出，还要保存到 plugin_callbacks 数据表中，主要字段介绍如下：
        // https://github.com/fresns/extensions/tree/main/QiNiu
        $uuid = $this->generateUuid(32);
        $data['plugin_unikey'] = 'QiNiu';
        $data['member_id'] = 0;
        $data['uuid'] = $uuid;
        $data['types'] = $type;
        $content['callbackType'] = $type;
        $content['dataType'] = 'object';
        $dataValue['storageId'] = 17;
        $dataValue['fileType'] = $scene;
        $dataValue['token'] = $token;
        $content['dataValue'] = $dataValue;
        $data['content'] = json_encode($content);
        FresnsPluginCallbacks::insert($data);

        return $this->pluginSuccess($output);
    }

    // 上传文件
    public function plgCmdUploadFileHandler($input)
    {
        $mode = $input['mode'];
        $fid = $input['fid'];
        $fidArr = json_decode($fid, true);
        $output = $input;
        foreach ($fidArr as $v) {
            $files = FresnsFiles::where('uuid', $v)->first();
            $qiNiuService = new QiNiuService($files['file_type']);
            $path = base_path();
            $path = $path.'/storage/app/public';
            $options = [];
            $options['file_type'] = $files['file_type'];
            $options['table_type'] = $files['table_type'];
            $newFilePath = FileSceneService::getFormalEditorPath($options);
            //获取最新的文件名
            $fileNameArr = explode('/', $files['file_path']);
            $fileName = end($fileNameArr);
            $newFile = '/'.$newFilePath.'/'.$fileName;
            $newPath = $path.$newFile;
            copy($path.$files['file_path'], $newPath);
            $qiNiuService->uploadLocalFile($newPath, $newFilePath.'/'.$fileName);
            FresnsFiles::where('uuid', $v)->update(['file_path' => $newFile]);
            unlink($path.$files['file_path']);

            //如果是视频文件，则需要生成一张封面图
            if ($files['file_type'] == 2) {
                $transService = new QiNiuTransService($files['file_type']);
                $dateStr = date('YmdHis', time());
                // 视频缩略图，转码参数来自配置表 video_screenshot
                $transAudioParams = ConfigHelper::fresnsConfigByItemKey('video_screenshot');
                $key = $newFilePath.'/'.$fileName;
                $saveAsKey = "$newFilePath"."/{$dateStr}.jpg";
                $id = $transService->vframe($key, $saveAsKey, $transAudioParams);
                if ($mode == 1) {
                    //生成一条视频封面图并存入 file_appends > video_cover 字段
                    FresnsFileAppends::where('file_id', $files['id'])->update(['video_cover' => '/'.$saveAsKey]);
                } else {
                    //查询 file_appends > video_cover 是否有封面图（字段为空则没有），没有则执行配置表 video_screenshot 键值，生成一条视频封面图并存入 file_appends > video_cover 字段
                    $videoCover = FresnsFileAppends::where('file_id', $files['id'])->value('video_cover');
                    if (empty($videoCover)) {
                        FresnsFileAppends::where('file_id', $files['id'])->update(['video_cover' => '/'.$saveAsKey]);
                    }
                }
            }

            //删除本地临时文件
            $cmd = FresnsCmdWordsConfig::FRESNS_CMD_PHYSICAL_DELETION_TEMP_FILE;
            $input['fid'] = $v;
            $resp = CmdRpcHelper::call(FresnsCmdWords::class, $cmd, $input);
        }

        return $this->pluginSuccess($output);
    }

    // 图片：获取带防盗链签名的地址
    public function plgCmdAntiLinkImageHandler($input)
    {
        $imagesBucketDomain = ConfigHelper::fresnsConfigByItemKey('image_bucket_domain');
        $fid = $input['fid'];
        $files = FresnsFiles::where('fid', $fid)->first();

        $append = FresnsFileAppends::where('file_id', $files['id'])->first();

        $imagesBucketDomain = ConfigHelper::fresnsConfigByItemKey('image_bucket_domain');
        $imagesThumbConfig = ConfigHelper::fresnsConfigByItemKey('image_thumb_config');
        $imagesThumbAvatar = ConfigHelper::fresnsConfigByItemKey('image_thumb_avatar');
        $imagesThumbRatio = ConfigHelper::fresnsConfigByItemKey('image_thumb_ratio');
        $imagesThumbSquare = ConfigHelper::fresnsConfigByItemKey('image_thumb_square');
        $imagesThumbBig = ConfigHelper::fresnsConfigByItemKey('image_thumb_big');

        $qiNiuImageService = new QiNiuImageService(1);
        $imageDefaultUrl = $imagesBucketDomain.$files['file_path'];
        $imageConfigUrl = $imagesBucketDomain.$files['file_path'].$imagesThumbConfig;
        $imageAvatarUrl = $imagesBucketDomain.$files['file_path'].$imagesThumbAvatar;
        $imageRatioUrl = $imagesBucketDomain.$files['file_path'].$imagesThumbRatio;
        $imageSquareUrl = $imagesBucketDomain.$files['file_path'].$imagesThumbSquare;
        $imageBigUrl = $imagesBucketDomain.$files['file_path'].$imagesThumbBig;
        $originalUrl = $imagesBucketDomain.$append['file_original_path'];

        $imageDefaultUrl = $qiNiuImageService->getImageDownloadUrl($imageDefaultUrl);
        $imageConfigUrl = $qiNiuImageService->getImageDownloadUrl($imageConfigUrl);
        $imageAvatarUrl = $qiNiuImageService->getImageDownloadUrl($imageAvatarUrl);
        $imageRatioUrl = $qiNiuImageService->getImageDownloadUrl($imageRatioUrl);
        $imageSquareUrl = $qiNiuImageService->getImageDownloadUrl($imageSquareUrl);
        $imageBigUrl = $qiNiuImageService->getImageDownloadUrl($imageBigUrl);
        $originalUrl = $qiNiuImageService->getImageDownloadUrl($originalUrl);

        $output['imageDefaultUrl'] = $imageDefaultUrl;
        $output['imageConfigUrl'] = $imageConfigUrl;
        $output['imageAvatarUrl'] = $imageAvatarUrl;
        $output['imageRatioUrl'] = $imageRatioUrl;
        $output['imageSquareUrl'] = $imageSquareUrl;
        $output['imageBigUrl'] = $imageBigUrl;
        $output['originalUrl'] = $originalUrl;

        return $this->pluginSuccess($output);
    }

    // 视频：获取带防盗链签名的地址
    public function plgCmdAntiLinkVideoHandler($input)
    {
        $fid = $input['fid'];
        $files = FresnsFiles::where('fid', $fid)->first();
        $append = FresnsFileAppends::where('file_id', $files['id'])->first();

        $videosBucketDomain = ConfigHelper::fresnsConfigByItemKey('video_bucket_domain');

        $videoCover = $videosBucketDomain.$append['video_cover'];
        $videoGif = $videosBucketDomain.$append['video_gif'];
        $videoUrl = $videosBucketDomain.$files['file_path'];
        $originalUrl = $videosBucketDomain.$append['file_original_path'];
        $qiNiuVideoService = new QiNiuVideoService(2);

        $videoCover = $qiNiuVideoService->getVideoDownloadUrl($videoCover);
        $videoGif = $qiNiuVideoService->getVideoDownloadUrl($videoGif);
        $videoUrl = $qiNiuVideoService->getVideoDownloadUrl($videoUrl);
        $originalUrl = $qiNiuVideoService->getVideoDownloadUrl($originalUrl);

        $output['videoCover'] = $videoCover;
        $output['videoGif'] = $videoGif;
        $output['videoUrl'] = $videoUrl;
        $output['originalUrl'] = $originalUrl;

        return $this->pluginSuccess($output);
    }

    // 音频：获取带防盗链签名的地址
    public function plgCmdAntiLinkAudioHandler($input)
    {
        $fid = $input['fid'];
        $files = FresnsFiles::where('fid', $fid)->first();
        $append = FresnsFileAppends::where('file_id', $files['id'])->first();

        $audiosBucketDomain = ConfigHelper::fresnsConfigByItemKey('audio_bucket_domain');

        $audioUrl = $audiosBucketDomain.$files['file_path'];
        $originalUrl = $audiosBucketDomain.$append['file_original_path'];

        $qiNiuAudioService = new QiNiuAudioService(3);
        $audioUrl = $qiNiuAudioService->getAudioDownloadUrl($audioUrl);
        $originalUrl = $qiNiuAudioService->getAudioDownloadUrl($originalUrl);

        $output['audioUrl'] = $audioUrl;
        $output['originalUrl'] = $originalUrl;

        return $this->pluginSuccess($output);
    }

    // 文档：获取带防盗链签名的地址
    public function plgCmdAntiLinkDocHandler($input)
    {
        $fid = $input['fid'];
        $files = FresnsFiles::where('fid', $fid)->first();
        $append = FresnsFileAppends::where('file_id', $files['id'])->first();

        $docsBucketDomain = ConfigHelper::fresnsConfigByItemKey('document_bucket_domain');

        $docUrl = $docsBucketDomain.$files['file_path'];
        $originalUrl = $docsBucketDomain.$append['file_original_path'];

        $qiNiuDocService = new QiNiuDocService(4);
        $docUrl = $qiNiuDocService->getDocDownloadUrl($docUrl);
        $originalUrl = $qiNiuDocService->getDocDownloadUrl($originalUrl);

        $output['documentUrl'] = $docUrl;
        $output['originalUrl'] = $originalUrl;

        return $this->pluginSuccess($output);
    }

    //
    /**
     *  凭 fid 在七牛云物理删除该文件，并将数据表 deleted_at 逻辑删除。
     *  https://developer.qiniu.com/kodo/1257/delete
     *  https://developer.qiniu.com/kodo/1276/data-format.
     */
    public function plgCmdPhysicalDeletionFileHandler($input)
    {
        $fid = $input['fid'];
        $files = FresnsFiles::where('fid', $fid)->first();
        FresnsFiles::where('fid', $fid)->delete();
        if ($files['file_type'] == 1) {
            $qiNiuBucketName = ConfigHelper::fresnsConfigByItemKey('image_bucket_name');
        }
        if ($files['file_type'] == 2) {
            $qiNiuBucketName = ConfigHelper::fresnsConfigByItemKey('video_bucket_name');
        }
        if ($files['file_type'] == 3) {
            $qiNiuBucketName = ConfigHelper::fresnsConfigByItemKey('audio_bucket_name');
        }
        if ($files['file_type'] == 4) {
            $qiNiuBucketName = ConfigHelper::fresnsConfigByItemKey('document_bucket_name');
        }
        $qiNiuService = new QiNiuService($files['file_type']);
        $key = substr($files['file_path'], 1);
        $res = $qiNiuService->deleteResource($qiNiuBucketName, $key);

        return $this->pluginSuccess();
    }

    /**
     * 转码
     */
    public function fresnsCmdQiniuTranscodingHandler($input)
    {
        $tableName = $input['tableName'];
        $insertId = $input['insertId'];
        if ($tableName == 'posts') {
            $postMoreJson = FresnsPosts::where('id', $insertId)->value('more_json');
            if (empty($postMoreJson)) {
                return $this->pluginError(ErrorCodeService::POST_EXIST_ERROR);
            }
            $postMoreArr = json_decode($postMoreJson, true);
            if (! empty($postMoreArr['files'])) {
                $video_transcode = ConfigHelper::fresnsConfigByItemKey('video_transcode');
                $audio_transcode = ConfigHelper::fresnsConfigByItemKey('audio_transcode');

                //1、根据主键 ID 查询 more_json > files 是否有文件，如果文件类型为 2 和 3，则执行下一步；
                foreach ($postMoreArr['files'] as $v) {
                    if ($v['type'] == 2) {
                        $transService = new QiNiuTransService(2);
                        $files = FresnsFiles::where('fid', $v['fid'])->first();
                        if (empty($files)) {
                            continue;
                        }
                        //2、查询文件 file_appends > transcoding_state 是否已经转码，已经转码则流程中止，未转码则下一步
                        $fileAppend = FresnsFileAppends::where('file_id', $files['id'])->first();
                        if ($fileAppend['transcoding_state'] != 1) {
                            continue;
                        }
                        $dateStr = date('YmdHis', time());
                        $key = substr($files['file_path'], 1);

                        $options = [];
                        $options['file_type'] = $files['file_type'];
                        $options['table_type'] = $files['table_type'];
                        $newFilePath = FileSceneService::getFormalEditorPath($options);
                        $saveAsKey = $newFilePath."/fresns-video-{$dateStr}.".$v['extension'];
                        $base64Data = [];
                        $base64Data['tableName'] = $tableName;
                        $base64Data['tableId'] = $insertId;
                        $base64Data['fileId'] = $files['fid'];
                        $base64Data['saveAsKey'] = $saveAsKey;
                        request()->offsetSet('callback_param', base64_encode(json_encode($base64Data)));
                        $transId = $transService->transVideo($key, $saveAsKey, $video_transcode, $tableName, $insertId);

                        if (! empty($transId)) {
                            FresnsFileAppends::where('file_id', $files['id'])->update(['transcoding_state' => 2]);
                        }
                    }
                    if ($v['type'] == 3) {
                        $transService = new QiNiuTransService(3);
                        $files = FresnsFiles::where('fid', $v['fid'])->first();
                        if (empty($files)) {
                            continue;
                        }
                        //2、查询文件 file_appends > transcoding_state 是否已经转码，已经转码则流程中止，未转码则下一步
                        $fileAppend = FresnsFileAppends::where('file_id', $files['id'])->first();
                        if ($fileAppend['transcoding_state'] != 1) {
                            continue;
                        }
                        $dateStr = date('YmdHis', time());
                        $key = substr($files['file_path'], 1);

                        $options = [];
                        $options['file_type'] = $files['file_type'];
                        $options['table_type'] = $files['table_type'];
                        $newFilePath = FileSceneService::getFormalEditorPath($options);
                        $saveAsKey = $newFilePath."/fresns-audio-{$dateStr}.".$v['extension'];
                        $base64Data = [];
                        $base64Data['tableName'] = $tableName;
                        $base64Data['tableId'] = $insertId;
                        $base64Data['fileId'] = $files['fid'];
                        $base64Data['saveAsKey'] = $saveAsKey;
                        request()->offsetSet('callback_param', base64_encode(json_encode($base64Data)));
                        $transId = $transService->transAudio($key, $saveAsKey, $audio_transcode, $tableName, $insertId);
                        if (! empty($transId)) {
                            FresnsFileAppends::where('file_id', $files['id'])->update(['transcoding_state' => 2]);
                        }
                    }
                }
            }
        }
        if ($tableName == 'comments') {
            $commentsMoreJson = FresnsComments::where('id', $insertId)->value('more_json');
            if (empty($commentsMoreJson)) {
                return $this->pluginError(ErrorCodeService::COMMENT_EXIST_ERROR);
            }
            $commentsMoreJsonArr = json_decode($commentsMoreJson, true);
            if (! empty($commentsMoreJsonArr['files'])) {
                $video_transcode = ConfigHelper::fresnsConfigByItemKey('video_transcode');
                $audio_transcode = ConfigHelper::fresnsConfigByItemKey('audio_transcode');
                //1、根据主键 ID 查询 more_json > files 是否有文件，如果文件类型为 2 和 3，则执行下一步；
                foreach ($commentsMoreJsonArr['files'] as $v) {
                    if ($v['type'] == 2) {
                        $transService = new QiNiuTransService(2);
                        $files = FresnsFiles::where('fid', $v['fid'])->first();
                        if (empty($files)) {
                            continue;
                        }
                        //2、查询文件 file_appends > transcoding_state 是否已经转码，已经转码则流程中止，未转码则下一步
                        $fileAppend = FresnsFileAppends::where('file_id', $files['id'])->first();
                        if ($fileAppend['transcoding_state'] != 1) {
                            continue;
                        }
                        $dateStr = date('YmdHis', time());
                        $key = substr($files['file_path'], 1);

                        $options = [];
                        $options['file_type'] = $files['file_type'];
                        $options['table_type'] = $files['table_type'];
                        $newFilePath = FileSceneService::getFormalEditorPath($options);
                        $saveAsKey = $newFilePath."/fresns-video-{$dateStr}.".$v['extension'];
                        $base64Data = [];
                        $base64Data['tableName'] = $tableName;
                        $base64Data['tableId'] = $insertId;
                        $base64Data['fileId'] = $files['fid'];
                        $base64Data['saveAsKey'] = $saveAsKey;
                        request()->offsetSet('callback_param', base64_encode(json_encode($base64Data)));
                        $transId = $transService->transVideo($key, $saveAsKey, $video_transcode, $tableName, $insertId);
                        if (! empty($transId)) {
                            FresnsFileAppends::where('file_id', $files['id'])->update(['transcoding_state' => 2]);
                        }
                    }
                    if ($v['type'] == 3) {
                        $transService = new QiNiuTransService(3);
                        $files = FresnsFiles::where('fid', $v['fid'])->first();
                        if (empty($files)) {
                            continue;
                        }
                        //2、查询文件 file_appends > transcoding_state 是否已经转码，已经转码则流程中止，未转码则下一步
                        $fileAppend = FresnsFileAppends::where('file_id', $files['id'])->first();
                        if ($fileAppend['transcoding_state'] != 1) {
                            continue;
                        }
                        $dateStr = date('YmdHis', time());
                        $key = substr($files['file_path'], 1);

                        $options = [];
                        $options['file_type'] = $files['file_type'];
                        $options['table_type'] = $files['table_type'];
                        $newFilePath = FileSceneService::getFormalEditorPath($options);
                        $saveAsKey = $newFilePath."/fresns-audio-{$dateStr}.".$v['extension'];
                        $base64Data = [];
                        $base64Data['tableName'] = $tableName;
                        $base64Data['tableId'] = $insertId;
                        $base64Data['fileId'] = $files['fid'];
                        $base64Data['saveAsKey'] = $saveAsKey;
                        request()->offsetSet('callback_param', base64_encode(json_encode($base64Data)));
                        $transId = $transService->transAudio($key, $saveAsKey, $audio_transcode, $tableName, $insertId);
                        if (! empty($transId)) {
                            FresnsFileAppends::where('file_id', $files['id'])->update(['transcoding_state' => 2]);
                        }
                    }
                }
            }
        }

        return $this->pluginSuccess();
    }

    public function generateUuid($length = 16, $range = '0123456789abcdef')
    {
        $chars = str_shuffle($range);

        $str = '';

        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, $size - 1)];
        }

        return $str;
    }
}
