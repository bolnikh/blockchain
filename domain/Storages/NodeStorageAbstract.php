<?php

declare(strict_types=1);

namespace Domain\Storages;

use App\Interfaces\ServiceInterface;
use Domain\Interfaces\NodeStorageInterface;

abstract class NodeStorageAbstract implements NodeStorageInterface, ServiceInterface
{
    public function deleteAll(string $iAmSure): void
    {
        if ($iAmSure != 'I am sure to delete all trx')
        {
            return;
        }
        foreach ($this->getList() as $node)
        {
            $this->delete($node);
        }
    }
}