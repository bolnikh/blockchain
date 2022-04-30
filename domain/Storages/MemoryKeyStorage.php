<?php


declare(strict_types=1);

namespace Domain\Storages;


use Domain\Interfaces\KeyStorageInterface;

class MemoryKeyStorage implements KeyStorageInterface
{

    private array $storage = [];

    public function get(string $key): string
    {
        return $this->storage[$key];
    }

    public function isExists(string $key): bool
    {
        return isset($this->storage[$key]);
    }

    public function delete(string $key): void
    {
        unset($this->storage[$key]);
    }

    public function set(string $key, string $value): void
    {
        $this->storage[$key] = $value;
    }
}