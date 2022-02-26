<?php

declare(strict_types=1);

namespace Domain\Storages;

use Domain\Node;


class NodeStorageFile extends NodeStorageAbstract
{
    private $storageDir = __DIR__.'/../../storage/files/nodes/';
    private $fileExt = 'txt';

    public function __construct(string $storageDir = '')
    {
        if ($storageDir) {
            $this->storageDir = $storageDir;
        }
    }

    public function nodeFileName(Node $node) : string
    {
        $hash = $node->getHash();
        return $this->storageDir.$hash.'.'.$this->fileExt;
    }

    public function store(Node $node): void
    {
        file_put_contents($this->nodeFileName($node), $node->toJson());
    }

    public function getList(bool $active = true): array
    {
        $files = scandir($this->storageDir);
        $nodes = [];

        foreach ($files as $file)
        {
            $path_parts = pathinfo($file);
            if ($path_parts['extension'] == $this->fileExt)
            {
                $node = new Node(json_decode(file_get_contents($this->storageDir.$file), true));
                if ($active == true) {
                    if ($node->isActive()) {
                        $nodes[] = $node;
                    }
                } else {
                    $nodes[] = $node;
                }
            }
        }

        return $nodes;
    }

    public function isExists(Node $node): bool
    {
        return file_exists($this->nodeFileName($node));
    }

    public function delete(Node $node): void
    {
        unlink($this->nodeFileName($node));
    }


}