<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Traits;

use App\Helpers\ConfigHelper;
use App\Helpers\FileHelper;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

trait QiNiuStorageTrait
{
    /**
     * 见 https://fresns.cn/database/dictionary/storages.html.
     */
    protected $storageId = 17;

    /**
     * Fresns.
     */
    protected $defaultBucketName = 'Fresns';

    /**
     * 单位：分钟
     */
    protected $defaultExpireTime = 3600;

    protected array $userConfig = [];

    public function getType(): int
    {
        return (int) $this->payload['type'];
    }

    public function setType(int $type)
    {
        $this->payload['type'] = $type;

        return $this;
    }

    public function isEnableStorage()
    {
        return $this->userConfig['storageConfigStatus'] ?? false;
    }

    public function isEnableAntiLink()
    {
        return $this->userConfig['antiLinkConfigStatus'] ?? false;
    }

    public function resetQiNiuConfig()
    {
        $userConfig = $this->getQiNiuStorageConfig();

        $qiniuConfig = config('fresns-qiniu-filesystems.disks.qiniu');
        $qiniuConfig = array_merge($qiniuConfig, $userConfig);

        config([
            'filesystems.default' => $qiniuConfig['driver'],
            'filesystems.disks.qiniu' => $qiniuConfig,
        ]);
    }

    public function getQiNiuStorageConfig()
    {
        $this->userConfig = FileHelper::fresnsFileStorageConfigByType($this->getType());

        return [
            'access_key' => $this->userConfig['secretId'],
            'secret_key' => $this->userConfig['secretKey'],
            'bucket' => $this->getBucketName(),
            'domain' => $this->getBucketDomain(),
        ];
    }

    public function getStorage(): ?\Illuminate\Filesystem\FilesystemAdapter
    {
        $this->resetQiNiuConfig();

        if (! $this->isEnableStorage()) {
            return null;
        }

        return Storage::disk('qiniu');
    }

    /**
     * @return null|\League\Flysystem\FilesystemAdapter|\Overtrue\Flysystem\Qiniu\QiniuAdapter
     */
    public function getAdapter()
    {
        return $this->getStorage()?->getAdapter();
    }

    public function getStorageId()
    {
        return $this->storageId;
    }

    public function getDefaultBucketName()
    {
        return $this->defaultBucketName;
    }

    public function getDefaultExpireTime()
    {
        return $this->defaultExpireTime;
    }

    /**
     * 生成七牛云防盗链，防盗链基于时间戳.
     *
     * @param  string  $url
     * @param  string  $antiLinkKey
     * @param  int  $deadline
     * @param  array  $query
     * @return void
     *
     * @see https://developer.qiniu.com/fusion/kb/1670/timestamp-hotlinking-prevention
     */
    public function getAntiLinkUrl(string $url, string $antiLinkKey, int $deadline, array $query = [])
    {
        $urlInfo = parse_url($url);

        if (empty($urlInfo['path'])) {
            return null;
        }

        $qiniuOriginUrl = sprintf('/%s', ltrim($urlInfo['path'], '/'));

        $accessUrl = $qiniuOriginUrl;
        $accessUrl = implode('/', array_map('rawurlencode', explode('/', $accessUrl)));

        $key = $antiLinkKey;

        $hexDeadline = dechex($deadline);
        $lowerHexDeadline = strtolower($hexDeadline);

        $signString = sprintf('%s%s%s', $key, $accessUrl, $lowerHexDeadline);

        $sign = strtolower(md5($signString));

        $querystring = http_build_query(array_merge($query, [
            'sign' => $sign,
            't' => $lowerHexDeadline,
        ]));

        if (str_contains($url, '?')) {
            $url .= "&{$querystring}";
        } else {
            $url .= "?{$querystring}";
        }

        return $url;
    }

    public function getAntiLinkKey()
    {
        return $this->userConfig['antiLinkKey'] ?? null;
    }

    public function getDeadline()
    {
        return time() + $this->getExpireSeconds();
    }

    public function getBucketDomain()
    {
        return $this->userConfig['bucketDomain'] ?? null;
    }

    public function getBucketName()
    {
        return $this->userConfig['bucketName'] ?? $this->getDefaultBucketName();
    }

    public function getExpireSeconds()
    {
        $expireMinuteTime = $this->userConfig['antiLinkExpire'] ?? (int) bcdiv($this->defaultExpireTime, 60);

        return (int) bcmul($expireMinuteTime, 60);
    }

    /**
     * 七牛云转码、生成视频截图.
     *
     * @param  \Qiniu\Auth  $auth
     * @param  string  $transParams
     * @param  string  $bucket
     * @param  string  $dir
     * @param  string  $key
     * @param  string  $filename
     * @param  string  $notifyUrl
     * @return array
     *
     * @see https://developer.qiniu.com/dora/api/persistent-data-processing-pfop#4
     */
    public function executeTranscoding(\Qiniu\Auth $auth, ?string $transParams, string $bucket, string $dir, string $key, string $filename, string $notifyUrl): ?array
    {
        if (empty($transParams)) {
            return null;
        }

        $key = ltrim($key, '/');

        $pfop = new \Qiniu\Processing\PersistentFop($auth);

        // 截图文件存放位置
        $filepath = sprintf('%s/%s', rtrim($dir, '/'), ltrim($filename, '/'));

        $saveAs = "$bucket:$filepath";

        $fops = $transParams.'|saveas/'.\Qiniu\base64_urlSafeEncode($saveAs);
        $pipeline = 'default.sys';
        $force = false;

        [$id, ] = $pfop->execute($bucket, $key, $fops, $pipeline, $notifyUrl, $force);

        return [
            'id' => $id,
            'path' => $filepath,
        ];
    }

    public function getTranscodingConfig()
    {
        return ConfigHelper::fresnsConfigByItemKeys([
            'video_transcode',
            'audio_transcode',
        ]);
    }

    public function getTranscondingConfigByFileType(int $type)
    {
        if (empty($this->transcodingConfig)) {
            $this->transcodingConfig = $this->getTranscodingConfig();
        }

        $key = match ($type) {
            default => null,
            File::TYPE_VIDEO => 'video_transcode',
            File::TYPE_AUDIO => 'audio_transcode',
        };

        return $this->transcodingConfig[$key] ?? null;
    }
}
