<?php

declare(strict_types=1);

namespace Domain\Storages;


class NodeStorageMemory extends NodeStorageAbstract
{
    private array $nodes = [];

    public function store(string $node): void
    {
        if (!in_array($node, $this->nodes)) {
            $this->nodes[] = $node;
        }
    }

    public function getList(): array
    {
        sort($this->nodes);
        return $this->nodes;
    }

    public function delete(string $node): void
    {
        $this->nodes = array_values(array_filter($this->nodes, fn ($m) => $m != $node));
    }


}