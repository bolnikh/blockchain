<?php

declare(strict_types=1);

namespace Domain\Actions;

use App\Classes\NodeDataTransfer;
use App\Classes\ServiceLocator;
use Domain\Interfaces\RunnableInterface;
use Domain\Interfaces\TrxStorageInterface;
use Domain\Node;

class RequestExternalNewTrx implements RunnableInterface
{
    private ServiceLocator $service;
    private TrxStorageInterface $trxStorage;
    private TrxStorageInterface $newTrxStorage;

    public function __construct(
        private Node $externalNode
    )
    {
        $this->service = ServiceLocator::instance();
        $this->trxStorage = $this->service->get('TrxStorage');
        $this->newTrxStorage = $this->service->get('NewTrxStorage');
    }

    public function run(): void
    {
        $ndt = new NodeDataTransfer($this->externalNode);



    }
}
