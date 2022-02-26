<?php

declare(strict_types=1);

namespace Domain\Actions;


use App\Classes\ServiceLocator;
use Domain\Interfaces\RunnableInterface;
use Domain\Interfaces\TransactionStorageInterface;
use Domain\TransactionExists;


class StoreExternalNewTrx implements RunnableInterface
{
    private ServiceLocator $service;
    private TransactionStorageInterface $trxStorage;
    private TransactionStorageInterface $newTrxStorage;

    public function __construct(
        private TransactionExists $trx
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