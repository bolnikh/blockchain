<?php


declare(strict_types=1);

namespace Domain\Storages;

use App\Interfaces\ServiceInterface;
use Domain\Interfaces\TrxStorageInterface;

abstract class TrxStorageAbstract implements TrxStorageInterface, ServiceInterface
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
        if ($iAmSure != 'I am sure to delete all trx')
        {
            return;
        }
        foreach ($this->getKeyList() as $key)
        {
            $this->delete($key);
        }
    }
}