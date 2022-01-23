<?php


declare(strict_types=1);

namespace Domain\Storages;


use Domain\Exceptions\TransactionNotExists;
use Domain\TransactionExists;
use Domain\TransactionNew;

class TransactionStorageMemory extends TransactionStorageAbstract
{
    private $transactions = [];

    public function store(TransactionExists|TransactionNew $trx): void
    {
        $this->transactions[$trx->hash] = $trx;
    }

    public function getKeyList(): array
    {
        $a = array_keys($this->transactions);
        sort($a);
        return $a;
    }

    public function isExists(string $key): bool
    {
        return isset($this->transactions[$key]);
    }

    public function get(string $key): TransactionExists|TransactionNew
    {
        if (!$this->isExists($key))
        {
            throw new TransactionNotExists('Can not get trx');
        }
        return $this->transactions[$key];
    }

    public function delete(string $key): void
    {
        unset($this->transactions[$key]);
    }



}