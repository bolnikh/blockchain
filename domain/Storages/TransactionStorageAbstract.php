<?php


declare(strict_types=1);

namespace Domain\Storages;

use Domain\Interfaces\TransactionStorageInterface;

abstract class TransactionStorageAbstract implements TransactionStorageInterface
{
    public function deleteAllByTtl(): void
    {
        foreach ($this->getKeyList() as $key)
        {
            $trx = $this->get($key);
            if (!$trx->verifyTtl())
            {
                $this->delete($key);
            }
        }
    }

    public function deleteAll(string $iAmSure): void
    {
        if ($iAmSure != 'I am sure to delete all transactions')
        {
            return;
        }
        foreach ($this->getKeyList() as $key)
        {
            $this->delete($key);
        }
    }
}