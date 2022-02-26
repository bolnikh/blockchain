<?php

declare(strict_types=1);

namespace Domain\Storages;


use Domain\Node;

class NodeStorageMemory extends NodeStorageAbstract
{
    private array $nodes = [];

    public function store(Node $node): void
    {
        if (!in_array($node, $this->nodes)) {
            $this->nodes[] = $node;
        }
    }

    public function getList(bool $active = true): array
    {
        sort($this->nodes);
        return $this->nodes;
    }

    public function delete(Node $node): void
    {
        $this->nodes = array_values(array_filter($this->nodes, fn ($m) => $m != $node));
    }


}