<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Services;

use App\Helpers\FileHelper;
use App\Models\File;
use App\Models\FileUsage;
use App\Models\PluginCallback;
use Fresns\DTO\DTO;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;

class AudioVideoTranscoding extends DTO
{
    use QiNiuStorageTrait;

    protected array $transcodingConfig = [];

    public function rules(): array
    {
        return [
            'tableName' => ['string', 'required'],
            'primaryId' => ['integer', 'required'],
            'changeType' => ['string', 'required', Rule::in('created', 'deleted')],
        ];
    }

    public function process()
    {
        $fileAppends = FileUsage::query()
            ->with('file')
            ->whereIn('file_type', [File::TYPE_VIDEO, File::TYPE_AUDIO])
            ->whereIn('table_name', ['posts', 'comments', 'conversation_messages'])
            ->where('id', $this->primaryId)
            ->get();

        foreach ($fileAppends as $fileAppend) {
            // 待转码的附件才进行转码
            if ($fileAppend->file->transcoding_state != File::TRANSCODING_STATE_WAIT) {
                continue;
            }

            /** @var \Overtrue\Flysystem\Qiniu\QiniuAdapter $storage */
            $storage = $this->setType($fileAppend->file_type)->getAdapter();

            $transParams = $this->getTranscondingConfigByFileType($fileAppend->file_type);

            // @see https://developer.qiniu.com/dora/api/persistent-data-processing-pfop#4
            // fops 数据处理命令列表，以;分隔，可以指定多个数据处理命令。
            $firstTransParams = head(explode(';', $transParams));
            $transCmdAndType = head(explode('|', $firstTransParams));
            $extension = explode('/', $transCmdAndType)[1] ?? '';

            $key = $fileAppend->file->path;

            $uuid = Str::uuid();

            $fileBasename = pathinfo($key, PATHINFO_BASENAME); // 七牛存储空间的文件名，包含后缀
            $fileExt = pathinfo($key, PATHINFO_EXTENSION); // 七牛存储空间的文件名的后缀，不含 "."
            $oldFilename = pathinfo($key, PATHINFO_FILENAME); // 七牛存储空间的文件名，不含后缀

            $filename = str_replace($fileExt, $extension, $fileBasename);

            // 设置保存文件的新文件名
            $filenameParts = explode('-', $oldFilename);
            if (! empty($filenameParts[2])) {
                $filename = str_replace(
                    [
                        $fileExt, $filenameParts[2],
                    ],
                    [
                        $extension, 'transcode',
                    ],
                    $fileBasename);
            }

            $result = $this->executeTranscoding(
                auth: $storage->getAuthManager(),
                transParams: $transParams,
                bucket: $this->getBucketName(),
                dir: FileHelper::fresnsFileStoragePath($fileAppend->file_type, $fileAppend->usage_type),
                key: $key,
                filename: $filename,
                notifyUrl: route('qiniu.transcoding.callback', ['uuid' => $uuid]),
            );

            if (is_null($result) || empty($result['id'])) {
                continue;
            }

            $this->savePluginCallback($result, $fileAppend->file->getFileInfo(), $uuid);

            $this->updateTranscodingState($fileAppend->file);
        }
    }

    public function savePluginCallback(array $result, array $uploadFileInfo, $uuid)
    {
        return PluginCallback::create([
            'plugin_unikey' => 'QiNiu',
            'uuid' => $uuid,
            'type' => PluginCallback::TYPE_CUSTOMIZE,
            'content' => [
                'sence' => 'transcoding',
                'pipline_id' => $result['id'],
                'save_path' => $result['path'],
                'file' => $uploadFileInfo,
            ],
            'is_use' => PluginCallback::IS_USE_FALSE,
            'use_plugin_unikey' => 'QiNiu',
        ]);
    }

    public function updateTranscodingState(File $file)
    {
        $file->update([
            'transcoding_state' => File::TRANSCODING_STATE_ING,
        ]);
    }
}
