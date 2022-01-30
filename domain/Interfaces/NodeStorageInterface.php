<?php


declare(strict_types=1);

namespace Domain\Interfaces;


interface NodeStorageInterface
{
    public function store(string $node) : void;

    public function getList() : array;

    public function delete(string $node) : void;
}