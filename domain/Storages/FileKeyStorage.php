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
        if (!is_dir($dir)) {
            mkdir($dir);
            if (!is_dir($dir)) {
                throw new FileKeyStorageException('Dir not exists');
            }
        }

    }

    public function getSubPath(string $key) : string
    {
        if ($this->subDirType == '') {
            return $key;
        }

        $need_chars = $this->getSubPathLength() - strlen($key) + 2; // 2 for branch _2
        if ($need_chars > 0) {
            $key = str_repeat('0', $need_chars).$key; // add leading 0000
        }

        $arr = explode(':', $this->subDirType);
        $res = [];
        $_key = $key;
        foreach ($arr as $n) {
            $res[] = substr($_key, 0, intval($n));
            $_key = substr($_key, intval($n));
        }

        return join(DIRECTORY_SEPARATOR, $res).DIRECTORY_SEPARATOR.$_key;
    }

    public function getSubPathLength() : int
    {
        if ($this->subDirType == '') {
            return 0;
        }

        $length = 0;
        $arr = explode(':', $this->subDirType);
        foreach ($arr as $n) {
            $length += intval($n);
        }

        return $length;
    }

    public function getFilePath(string $key, bool $createPath = false): string
    {
        if ($createPath) {
            $sp = explode(DIRECTORY_SEPARATOR, $this->getSubPath($key));
            array_pop($sp); // удаляем последний элемент сам ключ

            $dir_full = $this->dir;
            foreach ($sp as $dir) {
                $dir_full = $dir_full.DIRECTORY_SEPARATOR.$dir;
                if (!is_dir($dir_full)) {
                    mkdir($dir_full);
                }
            }
        }
        return $this->dir.DIRECTORY_SEPARATOR.$this->getSubPath($key).'.'.$this->ext;
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

    /**
     * Список всех ключей
     *
     * @return array keys
     */
    public function list() : array
    {
        $files = [];

        $this->listDir($this->dir, $files);

        return $this->filesToKeys($files);
    }

    private function listDir($dir, &$tmp_files)
    {
        if (substr($dir, strlen($dir) - 1, 1) != DIRECTORY_SEPARATOR) {
            $dir .= DIRECTORY_SEPARATOR;
        }
        $files = glob($dir . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->listDir($file, $tmp_files);
            } else {
                $tmp_files[] = $file;
            }
        }
    }

    private function filesToKeys($files) : array
    {
        $res = [];

        foreach ($files as $f) {
            $res[] = $this->getKeyFromFile($f);
        }

        return $res;
    }

    public function getKeyFromFile($file) : string
    {
        $pathParts = pathinfo($file);

        $dirClean = str_replace(DIRECTORY_SEPARATOR, '', $pathParts['dirname']);

        $spl = $this->getSubPathLength();
        return $spl > 0 ? substr($dirClean, -$spl).$pathParts['filename'] : $pathParts['filename'];
    }

    /**
     * Удаляет все данные
     */
    public function deleteAll(string $iAmSure) : void {
        if ($iAmSure != 'I am sure to delete all') {
            return;
        }

        $this->deleteDirContent($this->dir);
        //rmdir($this->dir);
    }

    private function deleteDirContent($dir)
    {
        if (substr($dir, strlen($dir) - 1, 1) != DIRECTORY_SEPARATOR) {
            $dir .= DIRECTORY_SEPARATOR;
        }
        $files = glob($dir . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            if (is_dir($file)) {
                $this->deleteDirContent($file);
                rmdir($file);
            } else {
                unlink($file);
            }
        }
    }
}
