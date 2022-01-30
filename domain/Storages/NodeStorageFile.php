<?php

declare(strict_types=1);

namespace Domain\Storages;

use Domain\Hash;


class NodeStorageFile extends NodeStorageAbstract
{
    private $storageDir = __DIR__.'/../../storage/files/';
    private $storageSubDir = 'nodes/';
    private $fileExt = 'txt';

    public function __construct(string $storageDir = '')
    {
        if ($storageDir) {
            $this->storageDir = $storageDir;
        }
    }

    public function nodeFileName(string $node) : string
    {
        $hash = Hash::getHash($node);
        return $this->storageDir.$this->storageSubDir.$hash.'.'.$this->fileExt;
    }

    public function store(string $node): void
    {
        file_put_contents($this->nodeFileName($node), $node);
    }

    public function getList(): array
    {
        $files = scandir($this->storageDir.$this->storageSubDir);
        $nodes = [];

        foreach ($files as $file)
        {
            $path_parts = pathinfo($file);
            if ($path_parts['extension'] == $this->fileExt)
            {
                $nodes[] = file_get_contents($this->storageDir.$this->storageSubDir.$file);
            }
        }

        sort($nodes);
        return $nodes;
    }

    public function isExists(string $node): bool
    {
        return file_exists($this->nodeFileName($node));
    }

    public function delete(string $node): void
    {
        unlink($this->nodeFileName($node));
    }


}