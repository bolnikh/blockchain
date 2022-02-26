<?php


declare(strict_types=1);

namespace Domain\Interfaces;


use Domain\Node;

interface NodeStorageInterface
{
    public function store(Node $node) : void;

    public function getList(bool $active = true) : array;

    public function delete(Node $node) : void;
}