<?php

namespace Plugins\QiNiu;

use Illuminate\Http\File;
use App\Models\File as FileModel;
use Plugins\QiNiu\Events\FileUpdateToQiNiuSuccessfual;

class FresnsUploadFiles
{
    protected $storage;

    protected $fids;

    protected $files;
    
    public function __construct(array $wordBody)
    {
        $this->validate($wordBody);

        $this->fids = json_decode($wordBody['fids'], true) ?? [];

        $this->getFiles();
    }

    public function validate(array $data)
    {
        \validator()->validate($data, [
            'fids' => 'required|string',
        ]);
    }

    public function getFiles()
    {
        if (empty($this->files)) {
            foreach ($this->fids as $fid) {
                $this->files[$fid] = FileModel::where('fid', $fid)->firstOrFail();
            }
        }
        
        return $this->files;
    }

    public function getStorage($fileType)
    {
        return new Storage($fileType);
    }

    public function upload()
    {
        $result = [];
        foreach ($this->getFiles() as $fileModel) {
            $filePath = $fileModel->file_path;

            $qiniuFilePath = $fileModel->file_path;

            // 生成视频封面图
            event(new FileUpdateToQiNiuSuccessfual($fileModel, $qiniuFilePath));

            $result[] = $fileModel->getFileInfo();
        }

        return $result;
    }
}
