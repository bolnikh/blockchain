<?php

declare(strict_types=1);

namespace Domain\Storages;

use Domain\Interfaces\NodeStorageInterface;

abstract class NodeStorageAbstract implements NodeStorageInterface
{
    public function deleteAll(string $iAmSure): void
    {
        if ($iAmSure != 'I am sure to delete all transactions')
        {
            return;
        }
        foreach ($this->getList() as $node)
        {
            $this->delete($node);
        }
    }
}