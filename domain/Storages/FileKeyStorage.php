<?php

declare(strict_types=1);

namespace Domain\Storages;


use Domain\Interfaces\KeyStorageInterface;

/**
 * Class FileKeyStorage
 * @package Domain\Storages
 *
 * $subDirType = ''|2|2:2|3:3:3 - структура поддиректорий для того чтобы все файлы не падали в одну директорию
 */
class FileKeyStorage implements KeyStorageInterface
{
    public function __construct(
        private string $dir,
        private string $subDirType,
        private string $ext
    )
    {
        if (!file_exists($dir)) {
            throw new FileKeyStorageException('Dir not exists');
        }

    }

    public function getSubPath(string $key) : string
    {
        if ($this->subDirType == '') {
            return $key;
        }

        $arr = explode(':', $this->subDirType);
        $res = [];
        $_key = $key;
        foreach ($arr as $n) {
            $res[] = substr($_key, 0, intval($n));
            $_key = substr($_key, intval($n));
        }

        return join('/', $res).'/'.$_key;
    }

    public function getFilePath(string $key, bool $createPath = false): string
    {
        return $this->dir.'/'.$this->getSubPath($key).'.'.$this->ext;
    }


    public function get(string $key): string
    {
        return file_get_contents($this->getFilePath($key));
    }

    public function isExists(string $key): bool
    {
        return file_exists($this->getFilePath($key));
    }

    public function delete(string $key): void
    {
        unlink($this->getFilePath($key));
    }

    public function set(string $key, string $value): void
    {
        file_put_contents($this->getFilePath($key, true), $value);
    }
}
