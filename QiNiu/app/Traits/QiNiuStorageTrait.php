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
    // 参见 https://fresns.cn/database/dictionary/storages.html
    protected $storageId = 17;

    // 存储空间名称
    protected $defaultBucketName = 'Fresns';

    // 私有空间 URL 有效期，单位 秒
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
        return $this->userConfig['antiLinkStatus'] ?? false;
    }

    public function resetQiNiuConfig()
    {
        $userConfig = $this->getQiNiuStorageConfig();

        $qiniuConfig = config('fresns-qiniu-filesystems.disks.qiniu', []);
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
            'driver' => 'qiniu',
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
     * 生成七牛云私有空间文件链接.
     *
     * @param  string  $url
     * @param  int  $deadline
     * @return void
     *
     * @see https://developer.qiniu.com/kodo/1241/php#private-get
     */
    public function getAntiLinkUrl(string $url, int $deadline)
    {
        $storage = $this->getAdapter();

        $url = $storage?->privateDownloadUrl($url, $deadline);

        return $url;
    }

    // 转码配置
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
}
