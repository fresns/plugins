<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\ServicesOld;

use App\Fresns\Api\Center\Common\LogService;
use App\Fresns\Api\Helpers\StrHelper;
use App\Helpers\ConfigHelper;
use Plugins\QiNiu\QiNiuConfig;
use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

class QiNiuService
{
    public $qiNiuAppId;
    public $qiNiuAppKey;
    public $qiNiuBucketDomain;
    public $qiNiuBucketName;
    public $qiNiuArea;
    public $qiNiuAuth;
    public $type;

    public function __construct($type)
    {
        $this->type = $type;
        $this->init();
        $this->qiNiuAuth = new Auth($this->qiNiuAppId, $this->qiNiuAppKey);
    }

    // 初始化（获取存储配置）
    public function init()
    {
        if ($this->type == 1) {
            $this->qiNiuAppId = ConfigHelper::fresnsConfigByItemKey('image_secret_id');
            $this->qiNiuAppKey = ConfigHelper::fresnsConfigByItemKey('image_secret_key');
            $this->qiNiuBucketDomain = ConfigHelper::fresnsConfigByItemKey('image_bucket_domain');
            $this->qiNiuBucketName = ConfigHelper::fresnsConfigByItemKey('image_bucket_name');
            $this->qiNiuArea = ConfigHelper::fresnsConfigByItemKey('image_bucket_area');
        }
        if ($this->type == 2) {
            $this->qiNiuAppId = ConfigHelper::fresnsConfigByItemKey('video_secret_id');
            $this->qiNiuAppKey = ConfigHelper::fresnsConfigByItemKey('video_secret_key');
            $this->qiNiuBucketDomain = ConfigHelper::fresnsConfigByItemKey('video_bucket_domain');
            $this->qiNiuBucketName = ConfigHelper::fresnsConfigByItemKey('video_bucket_name');
            $this->qiNiuArea = ConfigHelper::fresnsConfigByItemKey('video_bucket_area');
        }
        if ($this->type == 3) {
            $this->qiNiuAppId = ConfigHelper::fresnsConfigByItemKey('audio_secret_id');
            $this->qiNiuAppKey = ConfigHelper::fresnsConfigByItemKey('audio_secret_key');
            $this->qiNiuBucketDomain = ConfigHelper::fresnsConfigByItemKey('audio_bucket_domain');
            $this->qiNiuBucketName = ConfigHelper::fresnsConfigByItemKey('audio_bucket_name');
            $this->qiNiuArea = ConfigHelper::fresnsConfigByItemKey('audio_bucket_area');
        }
        if ($this->type == 4) {
            $this->qiNiuAppId = ConfigHelper::fresnsConfigByItemKey('document_secret_id');
            $this->qiNiuAppKey = ConfigHelper::fresnsConfigByItemKey('document_secret_key');
            $this->qiNiuBucketDomain = ConfigHelper::fresnsConfigByItemKey('document_bucket_domain');
            $this->qiNiuBucketName = ConfigHelper::fresnsConfigByItemKey('document_bucket_name');
            $this->qiNiuArea = ConfigHelper::fresnsConfigByItemKey('document_bucket_area');
        }
    }

    // 获取上传 token
    public function getUploadToken($type = QiNiuConfig::TYPE_IMAGE, $key, $expires = 3600)
    {
        $policy = $this->getPolicy($type);

        $token = $this->qiNiuAuth->uploadToken($this->qiNiuBucketName, $key, 86400, $policy);

        return $token;
    }

    // 上传本地文件
    public function uploadLocalFile($filePath, $key)
    {

        // 生成上传 Token
        $token = $this->qiNiuAuth->uploadToken($this->qiNiuBucketName);
        // 要上传文件的本地路径
        // $filePath = './php-logo.png';

        // 上传到七牛存储后保存的文件名
        //  $key = 'my-php-logo.png';

        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();

        // 调用 UploadManager 的 putFile 方法进行文件的上传，该方法会判断文件大小，进而决定使用表单上传还是分片上传，无需手动配置。
        [$ret, $err] = $uploadMgr->putFile($token, $key, $filePath);
        LogService::info('QiNIU-putFile-ret', $ret);
        LogService::info('QiNIU-putFile-err', $err);
    }

    // 获取防盗链链接
    public function getPrivateDownloadUrl($baseUrl, $expires = 3600)
    {
        $privateUrl = $this->qiNiuAuth->privateDownloadUrl($baseUrl, $expires);

        return $privateUrl;
    }

    // 删除文件
    public function deleteResource($bucket, $key)
    {
        $config = new Config();
        $bucketManager = new BucketManager($this->qiNiuAuth, $config);

        $res = $bucketManager->delete($bucket, $key);

        return $res;
    }

    public function stat($key, $bucket = null)
    {
        $config = new Config();
        $bucketManager = new BucketManager($this->qiNiuAuth, $config);
        if (empty($bucket)) {
            $bucket = $this->qiNiuBucketName;
        }

        [$ret,$err] = $bucketManager->stat($bucket, $key);

        $data = [];
        $data['ret'] = $ret;
        $data['err'] = $err;

        return $data;
    }

    /**
     * 列举资源文件
     * https://developer.qiniu.com/kodo/api/1284/list.
     */
    public function listFiles()
    {
        $prefix = '';   // 要列取文件的公共前缀
        $marker = '';   // 上次列举返回的位置标记，作为本次列举的起点信息。
        $limit = 500;   // 本次列举的条目数，，范围为 1-1000
        $delimiter = '/';

        $bucketManager = new BucketManager($this->qiNiuAuth);

        // 列举文件
        [$ret, $err] = $bucketManager->listFiles($this->qiNiuBucketName, $prefix, $marker, $limit, $delimiter);

        $res = $ret['items'] ?? [];

        return $res;
    }

    // 上传策略
    // https://developer.qiniu.com/kodo/1235/vars#xvar
    public function getPolicy($type)
    {
        if ($type == QiNiuConfig::TYPE_IMAGE) {
            $service = new QiNiuImageService($type);

            return $service->getPolicy();
        }

        $defaultReturnBody = '{
            "name": $(fname),
            "size": $(fsize),
            "hash": $(etag)
        }';

        $policy = [
            'returnBody'    => $defaultReturnBody,
        ];

        return $policy;
    }

    // 获取资源 URI
    public function getEncodedEntryURI($bucket, $key)
    {
        $entry = "$bucket:$key";
        $encodedEntryURI = urlsafe_base64_encode($entry);

        return $encodedEntryURI;
    }

    // 获取上传限制
    public function getUploadLimit()
    {
        $infoArr = [];

        $limitFieldArr = [
            'image_ext',
            'image_max_size',
            'video_ext',
            'video_max_size',
            'video_max_time',
            'audio_ext',
            'audio_max_time',
            'audio_max_size',
            'document_ext',
            'document_max_size',
        ];
        foreach ($limitFieldArr as $limitField) {
            $infoArr[$limitField] = ConfigHelper::fresnsConfigByItemKey($limitField);
        }

        return $infoArr;
    }

    //json 主表文件格式替换
    public static function updateJsonFiles($filesJson, $fid, $saveAsKey, $videosBucketDomain, $audiosBucketDomain, $mime)
    {
        $moreJsonArr = json_decode($filesJson, true);
        $fileArr = [];
        foreach ($moreJsonArr['files'] as $v) {
            if ($v['fid'] == $fid) {
                if ($v['type'] == 2) {
                    $v['videoUrl'] = $videosBucketDomain.'/'.$saveAsKey;
                    $v['mime'] = $mime;
                    $v['transcodingState'] = 3;
                }
                if ($v['type'] == 3) {
                    $v['audioUrl'] = $audiosBucketDomain.'/'.$saveAsKey;
                    $v['mime'] = $mime;
                    $v['transcodingState'] = 3;
                }
            }
            $fileArr[] = $v;
        }
        $data['files'] = $fileArr;
        if (! empty($moreJsonArr['icons'])) {
            $data['icons'] = $moreJsonArr['icons'];
        }
        $json = json_encode($data);

        return $json;
    }

    //json log表文件格式替换
    public static function updateLogsJsonFiles($filesJson, $fid, $saveAsKey, $videosBucketDomain, $audiosBucketDomain, $mime)
    {
        $moreJsonArr = json_decode($filesJson, true);
        $fileArr = [];
        foreach ($moreJsonArr as $v) {
            if ($v['fid'] == $fid) {
                if ($v['type'] == 2) {
                    $v['videoUrl'] = $videosBucketDomain.'/'.$saveAsKey;
                    $v['mime'] = $mime;
                    $v['transcodingState'] = 3;
                }
                if ($v['type'] == 3) {
                    $v['audioUrl'] = $audiosBucketDomain.'/'.$saveAsKey;
                    $v['mime'] = $mime;
                    $v['transcodingState'] = 3;
                }
            }
            $fileArr[] = $v;
        }

        $json = json_encode($fileArr);

        return $json;
    }

    public function generatQiNiuKey($type)
    {
        $randString = StrHelper::randString(10);
        switch ($type) {
            case 1:
                $key = 'images/';
                break;
            case 2:
                $key = 'video/';
                break;
            case 3:
                $key = 'audio/';
                break;
            default:
                $key = 'document/';
                break;
        }
        $key = $key.$randString;
        $key = $randString;

        return $key;
    }
}
