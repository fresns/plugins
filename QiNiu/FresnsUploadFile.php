<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu;

use App\Models\File as FileModel;
use Illuminate\Http\File;
use Plugins\QiNiu\Events\FileUpdateToQiNiuSuccessfual;

class FresnsUploadFile
{
    protected $storage;

    public function __construct(array $wordBody)
    {
        $this->validate($wordBody);

        $this->fid = $wordBody['fid'];

        $this->file = $this->getFile();
    }

    public function validate(array $data)
    {
        \validator()->validate($data, [
            'fid' => 'required|string',
        ]);
    }

    public function getFile()
    {
        if (empty($this->file)) {
            $this->file = FileModel::where('fid', $this->fid)->firstOrFail();
        }

        return $this->file;
    }

    public function getFileAppend()
    {
        return $this->getFile()->fileAppend;
    }

    public function getStorage()
    {
        if (empty($this->storage)) {
            $this->storage = new Storage($this->file->file_type);
        }

        return $this->storage->getStorage();
    }

    public function upload()
    {
        $fileModel = $this->getFile();

        $filePath = sprintf('public/%s', $fileModel->file_path);

        // 读取本地文件信息
        $file = new File(storage_path('app/'.$filePath));

        // 保存本地文件到七牛云
        $qiniuFilePath = $this->getStorage()->putFileAs($fileModel->getDestinationPath(), $file, $file->getFilename());

        // 保存七牛云文件路径
        event(new FileUpdateToQiNiuSuccessfual($fileModel, $qiniuFilePath));

        // 删除本地文件
        \Storage::disk('local')->delete($filePath);

        return $this->getFile()->getFileInfo();
    }
}
