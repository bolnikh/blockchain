<?php

declare(strict_types=1);

namespace Domain\Actions;


use App\Classes\ServiceLocator;
use Domain\Interfaces\RunnableInterface;
use Domain\Interfaces\TrxStorageInterface;
use Domain\TrxExists;


class StoreExternalNewTrx implements RunnableInterface
{
    private ServiceLocator $service;
    private TrxStorageInterface $trxStorage;
    private TrxStorageInterface $newTrxStorage;

    public function __construct(
        private TrxExists $trx
    )
    {
        $this->service = ServiceLocator::instance();
        $this->trxStorage = $this->service->get('TrxStorage');
        $this->newTrxStorage = $this->service->get('NewTxStorage');

    }

    public function run() : void
    {
        $this->trxStorage->store($this->trx);
        $this->newTrxStorage->store($this->trx);
    }
}