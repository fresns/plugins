<?php

namespace Plugins\QiNiu;

use App\Models\File;
use Illuminate\Validation\Rule;

class QiNiu
{
    /**
     * 见 https://fresns.cn/database/dictionary/storages.html
     */
    const STORAGE_ID = 17;

    /**
     * Fresns
     */

    const DEFAULT_BUCKET_NAME = 'Fresns';

    /**
     * 单位：分钟
     */
    const DEFAULT_EXPIRE_TIME = 3600;
    
    protected int $type;
    
    public function __construct(array $wordBody)
    {
        $this->validate($wordBody);

        $this->type = $wordBody['type'];
        $this->expireTime = $wordBody['expireTime'] ?? QiNiu::DEFAULT_EXPIRE_TIME;

        $this->storage = new Storage($this->type);
    }

    public function validate(array $data)
    {
        \validator()->validate($data, [
            'name' => 'nullable|name',
            'type' => ['required', 'integer', Rule::in(array_keys(File::TYPE_MAP))],
            'expireTime' => 'nullable|integer',
        ]);
    }

    public function getStorageId()
    {
        return QiNiu::STORAGE_ID;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->storage->getConfig()['bucket'] ?? QiNiu::DEFAULT_BUCKET_NAME;
    }

    public function getExpireTime()
    {
        return $this->expireTime;
    }

    public function __call(string $method, array $args)
    {
        return $this->storage->$method(...$args);
    }
}
