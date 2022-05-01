<?php

namespace Plugins\QiNiu;

use App\Models\File;
use Qiniu\Processing\PersistentFop;

class Storage
{
    public function __construct(int $type)
    {
        $this->type = $type;
    }

    public function resetConfig()
    {
        $qiniuConfig = config('fresns-qiniu-filesystems.disks.qiniu');

        $userConfig = File::getFileStorageConfigByFileType($this->getType());

        $qiniuConfig = array_merge($qiniuConfig, $userConfig);

        config([
            'filesystems.disks.qiniu' => $qiniuConfig,
        ]);
    }

    public function getType()
    {
        return $this->type;
    }

    public function getStorage()
    {
        $this->resetConfig();

        return \Storage::disk('qiniu');
    }

    public function getAdapter()
    {
        return $this->getStorage()->getAdapter();
    }

    public function __call(string $method, array $args)
    {
        if (method_exists($this->getStorage(), $method)) {
            return $this->getStorage()->$method(...$args);
        }

        return $this->getAdapter()->$method(...$args);
    }

    public function getPersistentFop()
    {
        $auth = $this->getAdapter()->getAuthManager();

        return new PersistentFop($auth);
    }
}