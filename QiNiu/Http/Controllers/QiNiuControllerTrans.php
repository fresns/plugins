<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Plugins\QiNiu\ServicesOld\QiNiuService;
use App\Helpers\ConfigHelper;
use App\Fresns\Api\Center\Common\LogService;
use Plugins\QiNiu\ServicesOld\QiNiuTransService;
use App\Fresns\Api\FsDb\FresnsFiles\FresnsFiles;
use App\Fresns\Api\FsDb\FresnsPosts\FresnsPosts;
use App\Fresns\Api\Base\Controllers\BaseController;
use App\Fresns\Api\FsDb\FresnsComments\FresnsComments;
use App\Fresns\Api\FsDb\FresnsPostLogs\FresnsPostLogs;
use App\Fresns\Api\FsDb\FresnsCommentLogs\FresnsCommentLogs;
use App\Fresns\Api\FsDb\FresnsFileAppends\FresnsFileAppends;

class QiNiuControllerTrans extends BaseController
{
    public function __construct()
    {
    }

    // 音视频转码回调
    // 1、将转码前的「原文件路径」填入 file_appends > file_original_path
    // 2、将转码后的「新文件路径」填入 files > file_path
    // 3、修改文件转码状态 file_appends > transcoding_state
    public function transNotify(Request $request)
    {
        $callback = $request->input('callback_param');
        LogService::info('qiniu transNotify callback info', $request);
        if ($callback) {
            $itemArr = json_decode(base64_decode($callback), true);
            $transId = Cache::get($itemArr['tableName'].'_'.$itemArr['tableId']);
            $files = FresnsFiles::where('uuid', $itemArr['fileId'])->first();
            $transService = new QiNiuTransService($files['file_type']);
            $transArr = $transService->searchStatus($transId);
            if (! empty($transArr['error'])) {
                $input = [
                    'transcoding_state' => 4,
                ];
                FresnsFileAppends::where('file_id', $files['id'])->update($input);
            } else {
                if (! empty($transArr['ret'])) {
                    $filesAppend = FresnsFileAppends::where('file_id', $files['id'])->first();
                    $ret = $transArr['ret'];
                    if ($ret['code'] == 0) {
                        $qiNiuService = new QiNiuService($files['file_type']);
                        $key = null;
                        foreach ($ret['items'] as $v) {
                            $key = $v['key'];
                        }
                        $statRes = $qiNiuService->stat($key);
                        $file_mime = $filesAppend['file_mime'];
                        if (! empty($statRes['ret'])) {
                            $file_mime = $statRes['ret']['mimeType'];
                        }
                        FresnsFiles::where('uuid', $itemArr['fileId'])->update(['file_path' => '/'.$itemArr['saveAsKey']]);

                        $input = [
                            'transcoding_state' => 3,
                            'file_original_path' => $files['file_path'],
                            'file_mime' => $file_mime,
                        ];
                        FresnsFileAppends::where('file_id', $files['id'])->update($input);
                        //修改帖子或者评论内容
                        $videosBucketDomain = ConfigHelper::fresnsConfigByItemKey('video_bucket_domain');
                        $audiosBucketDomain = ConfigHelper::fresnsConfigByItemKey('audio_bucket_domain');
                        if ($itemArr['tableName'] == 'posts') {
                            //主表文件json替换
                            $moreJson = FresnsPosts::where('id', $itemArr['tableId'])->value('more_json');
                            $json = $qiNiuService->updateJsonFiles($moreJson, $itemArr['fileId'], $itemArr['saveAsKey'], $videosBucketDomain, $audiosBucketDomain, $file_mime);
                            FresnsPosts::where('id', $itemArr['tableId'])->update(['more_json' => $json]);
                            //logs表json替换
                            $logsMoreJson = FresnsPostLogs::where('id', $files['table_id'])->value('files_json');
                            $logsJson = $qiNiuService->updateLogsJsonFiles($logsMoreJson, $itemArr['fileId'], $itemArr['saveAsKey'], $videosBucketDomain, $audiosBucketDomain, $file_mime);
                            FresnsPostLogs::where('id', $files['table_id'])->update(['files_json' => $logsJson]);
                        }
                        if ($itemArr['tableName'] == 'comments') {
                            $moreJson = FresnsComments::where('id', $itemArr['tableId'])->value('more_json');
                            $json = $qiNiuService->updateJsonFiles($moreJson, $itemArr['fileId'], $itemArr['saveAsKey'], $videosBucketDomain, $audiosBucketDomain, $file_mime);
                            FresnsComments::where('id', $itemArr['tableId'])->update(['more_json' => $json]);
                            //log表替换
                            $logsMoreJson = FresnsCommentLogs::where('id', $files['table_id'])->value('files_json');
                            $logsJson = $qiNiuService->updateLogsJsonFiles($logsMoreJson, $itemArr['fileId'], $itemArr['saveAsKey'], $videosBucketDomain, $audiosBucketDomain, $file_mime);
                            FresnsCommentLogs::where('id', $files['table_id'])->update(['files_json' => $logsJson]);
                        }
                    } else {
                        foreach ($ret['items'] as $v) {
                            $input = [
                                'transcoding_state' => 4,
                                'transcoding_reason' => $v['error'] ?? null,
                            ];
                            FresnsFileAppends::where('file_id', $files['id'])->update($input);
                        }
                    }
                }
            }
        }
        $this->success();
    }
}
