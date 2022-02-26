<?php


declare(strict_types=1);

namespace Domain\Storages;


use Domain\Exceptions\TrxNotExists;
use Domain\TrxExists;
use Domain\TrxNew;

class TrxStorageMemory extends TrxStorageAbstract
{
    private $trx = [];

    public function store(TrxExists|TrxNew $trx): void
    {
        $this->trx[$trx->hash] = $trx;
    }

    public function getKeyList(): array
    {
        $a = array_keys($this->trx);
        sort($a);
        return $a;
    }

    public function isExists(string $key): bool
    {
        return isset($this->trx[$key]);
    }

    public function get(string $key): TrxExists|TrxNew
    {
        if (!$this->isExists($key))
        {
            throw new TrxNotExists('Can not get trx');
        }
        return $this->trx[$key];
    }

    public function delete(string $key): void
    {
        unset($this->trx[$key]);
    }



}